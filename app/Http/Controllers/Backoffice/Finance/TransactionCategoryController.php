<?php

namespace App\Http\Controllers\Backoffice\Finance;

use App\Http\Controllers\Controller;
use App\Models\TransactionCategory;
use App\Http\Requests\Backoffice\Finance\TransactionCategory\TransactionCategoryStoreRequest;
use App\Http\Requests\Backoffice\Finance\TransactionCategory\TransactionCategoryUpdateRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionCategoryController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of transaction categories.
     */
    public function index(Request $request)
    {
        $agencyId = Auth::guard('backoffice')->user()->agency_id;

        $query = TransactionCategory::where('agency_id', $agencyId);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Sort
        $sort = $request->get('sort', 'latest');
        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } elseif ($sort === 'name_asc') {
            $query->orderBy('name', 'asc');
        } elseif ($sort === 'name_desc') {
            $query->orderBy('name', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $categories = $query->paginate(15)->withQueryString();

        return view('backoffice.finance.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new transaction category.
     */
    public function create()
    {
        return view('backoffice.finance.categories.partials._modal_create');
    }

    /**
     * Store a newly created transaction category.
     */
    public function store(TransactionCategoryStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $data['agency_id'] = Auth::guard('backoffice')->user()->agency_id;

            $category = TransactionCategory::create($data);
            
            // FIXED: Use correct module name 'transaction-category' and the actual category object
            $this->createNotification('store', 'transaction-category', $category);
            
            DB::commit();

            return redirect()
                ->route('backoffice.finance.categories.index')
                ->with('toast', [
                    'title' => 'Créé',
                    'message' => 'Catégorie créée avec succès.',
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
     * Display the specified transaction category.
     */
    public function show(TransactionCategory $transactionCategory)
    {
       // $this->authorize('view', $transactionCategory);

        $transactionCategory->load(['transactions' => function($q) {
            $q->latest()->limit(10);
        }]);

        return view('backoffice.finance.categories.show', compact('transactionCategory'));
    }

    /**
     * Show the form for editing the specified transaction category.
     */
    public function edit(TransactionCategory $transactionCategory)
    {
       // $this->authorize('update', $transactionCategory);

        return view('backoffice.finance.categories.partials._modal_edit', compact('transactionCategory'));
    }

    /**
     * Update the specified transaction category.
     */
    public function update(TransactionCategoryUpdateRequest $request, TransactionCategory $transactionCategory)
    {
       // $this->authorize('update', $transactionCategory);

        try {
            DB::beginTransaction();

            $transactionCategory->update($request->validated());
            
            // ADDED: Create notification for update
            $this->createNotification('update', 'transaction-category', $transactionCategory);

            DB::commit();

            return redirect()
                ->route('backoffice.finance.categories.show', $transactionCategory)
                ->with('toast', [
                    'title' => 'Mis à jour',
                    'message' => 'Catégorie mise à jour avec succès.',
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
     * Remove the specified transaction category.
     */
    public function destroy(TransactionCategory $transactionCategory)
    {
        $this->authorize('delete', $transactionCategory);

        try {
            DB::beginTransaction();

            // Check if category has transactions
            if ($transactionCategory->transactions()->count() > 0) {
                return redirect()->back()->with('toast', [
                    'title' => 'Erreur',
                    'message' => 'Impossible de supprimer une catégorie qui a des transactions.',
                    'dot' => '#dc3545',
                    'delay' => 3500,
                    'time' => 'now',
                ]);
            }

            // Store category data for notification before delete
            $categoryData = clone $transactionCategory;
            $transactionCategory->delete();
            
            // ADDED: Create notification for delete
            $this->createNotification('destroy', 'transaction-category', $categoryData);
            
            DB::commit();

            return redirect()
                ->route('backoffice.finance.categories.index')
                ->with('toast', [
                    'title' => 'Supprimé',
                    'message' => 'Catégorie supprimée avec succès.',
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
}