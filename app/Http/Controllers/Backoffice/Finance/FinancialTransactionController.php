<?php

namespace App\Http\Controllers\Backoffice\Finance;

use App\Http\Controllers\Controller;
use App\Models\FinancialTransaction;
use App\Models\FinancialAccount;
use App\Models\TransactionCategory;
use App\Http\Requests\Backoffice\Finance\FinancialTransaction\FinancialTransactionStoreRequest;
use App\Http\Requests\Backoffice\Finance\FinancialTransaction\FinancialTransactionUpdateRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FinancialTransactionController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of financial transactions.
     */
    public function index(Request $request)
    {
        $agencyId = Auth::guard('backoffice')->user()->agency_id;

        $query = FinancialTransaction::where('agency_id', $agencyId)
            ->with(['account', 'category', 'createdBy']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%")
                  ->orWhereHas('account', function ($sub) use ($search) {
                      $sub->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('category', function ($sub) use ($search) {
                      $sub->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by account
        if ($request->filled('account_id')) {
            $query->where('financial_account_id', $request->account_id);
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('transaction_category_id', $request->category_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        // Filter by amount range
        if ($request->filled('amount_min')) {
            $query->where('amount', '>=', $request->amount_min);
        }
        if ($request->filled('amount_max')) {
            $query->where('amount', '<=', $request->amount_max);
        }

        // Sort
        $sort = $request->get('sort', 'latest');
        if ($sort === 'oldest') {
            $query->orderBy('date', 'asc');
        } elseif ($sort === 'date_asc') {
            $query->orderBy('date', 'asc');
        } elseif ($sort === 'date_desc') {
            $query->orderBy('date', 'desc');
        } elseif ($sort === 'amount_asc') {
            $query->orderBy('amount', 'asc');
        } elseif ($sort === 'amount_desc') {
            $query->orderBy('amount', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $transactions = $query->paginate(15)->withQueryString();

        // Get accounts and categories for filters
        $accounts = FinancialAccount::where('agency_id', $agencyId)->orderBy('name')->get();
        $categories = TransactionCategory::where('agency_id', $agencyId)->orderBy('name')->get();

        return view('backoffice.finance.transactions.index', compact('transactions', 'accounts', 'categories'));
    }

    /**
     * Show the form for creating a new financial transaction.
     */
    public function create()
    {
        $agencyId = Auth::guard('backoffice')->user()->agency_id;

        $accounts = FinancialAccount::where('agency_id', $agencyId)->orderBy('name')->get();
        $categories = TransactionCategory::where('agency_id', $agencyId)->orderBy('name')->get();

        return view('backoffice.finance.transactions.partials._modal_create', compact('accounts', 'categories'));
    }

    /**
     * Store a newly created financial transaction.
     */
    public function store(FinancialTransactionStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $data['agency_id'] = Auth::guard('backoffice')->user()->agency_id;
            $data['created_by'] = Auth::guard('backoffice')->id();

            $transaction = FinancialTransaction::create($data);
            
            // FIXED: Use correct module name 'financial-transaction' and the actual transaction object
            $this->createNotification('store', 'financial-transaction', $transaction);
            
            DB::commit();

            return redirect()
                ->route('backoffice.finance.transactions.show', $transaction)
                ->with('toast', [
                    'title' => 'Créé',
                    'message' => 'Transaction créée avec succès.',
                    'dot' => '#198754',
                    'delay' => 3500,
                    'time' => 'now',
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('toast', [
                'title' => 'Erreur',
                'message' => 'Erreur lors de la création: ' . $e->getMessage(),
                'dot' => '#dc3545',
                'delay' => 3500,
                'time' => 'now',
            ]);
        }
    }

    /**
     * Display the specified financial transaction.
     */
    public function show(FinancialTransaction $financialTransaction)
    {
        //$this->authorize('view', $financialTransaction);

        $financialTransaction->load(['account', 'category', 'createdBy', 'related']);

        return view('backoffice.finance.transactions.show', compact('financialTransaction'));
    }

    /**
     * Show the form for editing the specified financial transaction.
     */
    public function edit(FinancialTransaction $financialTransaction)
    {
        //$this->authorize('update', $financialTransaction);

        $agencyId = Auth::guard('backoffice')->user()->agency_id;

        $accounts = FinancialAccount::where('agency_id', $agencyId)->orderBy('name')->get();
        $categories = TransactionCategory::where('agency_id', $agencyId)->orderBy('name')->get();

        return view('backoffice.finance.transactions.partials._modal_edit', compact('financialTransaction', 'accounts', 'categories'));
    }

    /**
     * Update the specified financial transaction.
     */
    public function update(FinancialTransactionUpdateRequest $request, FinancialTransaction $financialTransaction)
    {
       // $this->authorize('update', $financialTransaction);

        try {
            DB::beginTransaction();

            $financialTransaction->update($request->validated());
            
            // ADDED: Create notification for update
            $this->createNotification('update', 'financial-transaction', $financialTransaction);

            DB::commit();

            return redirect()
                ->route('backoffice.finance.transactions.show', $financialTransaction)
                ->with('toast', [
                    'title' => 'Mis à jour',
                    'message' => 'Transaction mise à jour avec succès.',
                    'dot' => '#0d6efd',
                    'delay' => 3500,
                    'time' => 'now',
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('toast', [
                'title' => 'Erreur',
                'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage(),
                'dot' => '#dc3545',
                'delay' => 3500,
                'time' => 'now',
            ]);
        }
    }

    /**
     * Remove the specified financial transaction.
     */
    public function destroy(FinancialTransaction $financialTransaction)
    {
        //$this->authorize('delete', $financialTransaction);

        try {
            DB::beginTransaction();
            
            // Store transaction data for notification before delete
            $transactionData = clone $financialTransaction;
            $financialTransaction->delete();
            
            // ADDED: Create notification for delete
            $this->createNotification('destroy', 'financial-transaction', $transactionData);
            
            DB::commit();

            return redirect()
                ->route('backoffice.finance.transactions.index')
                ->with('toast', [
                    'title' => 'Supprimé',
                    'message' => 'Transaction supprimée avec succès.',
                    'dot' => '#dc3545',
                    'delay' => 3500,
                    'time' => 'now',
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('toast', [
                'title' => 'Erreur',
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage(),
                'dot' => '#dc3545',
                'delay' => 3500,
                'time' => 'now',
            ]);
        }
    }

    /**
     * Get summary statistics.
     */
    public function summary(Request $request)
    {
        $agencyId = Auth::guard('backoffice')->user()->agency_id;

        $query = FinancialTransaction::where('agency_id', $agencyId);

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $totalIncome = $query->clone()->where('type', 'income')->sum('amount');
        $totalExpense = $query->clone()->where('type', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;

        return response()->json([
            'total_income' => number_format($totalIncome, 2, ',', ' ') . ' MAD',
            'total_expense' => number_format($totalExpense, 2, ',', ' ') . ' MAD',
            'balance' => number_format($balance, 2, ',', ' ') . ' MAD',
        ]);
    }
}