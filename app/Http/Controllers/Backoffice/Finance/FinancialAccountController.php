<?php

namespace App\Http\Controllers\Backoffice\Finance;

use App\Http\Controllers\Controller;
use App\Models\FinancialAccount;
use App\Http\Requests\Backoffice\Finance\FinancialAccount\FinancialAccountStoreRequest;
use App\Http\Requests\Backoffice\Finance\FinancialAccount\FinancialAccountUpdateRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FinancialAccountController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of financial accounts.
     */
    public function index(Request $request): View
    {
        // ✅ Vérifier la permission VIEW
        if (!auth()->user()->can('financial-accounts.general.view')) {
            abort(403, 'Vous n\'avez pas la permission de voir les comptes financiers.');
        }

        $agencyId = Auth::guard('backoffice')->user()->agency_id;

        $query = FinancialAccount::where('agency_id', $agencyId);

        // 🔎 Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('rib', 'like', "%{$search}%");
            });
        }

        // 🏷️ Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // ⭐ Filter by default
        if ($request->filled('is_default')) {
            $query->where('is_default', $request->is_default);
        }

        // 🔤 Sort
        $sort = $request->get('sort', 'latest');
        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } elseif ($sort === 'name_asc') {
            $query->orderBy('name', 'asc');
        } elseif ($sort === 'name_desc') {
            $query->orderBy('name', 'desc');
        } elseif ($sort === 'balance_asc') {
            $query->orderBy('current_balance', 'asc');
        } elseif ($sort === 'balance_desc') {
            $query->orderBy('current_balance', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $accounts = $query->paginate(15)->withQueryString();

        // ✅ Passer les permissions à la vue
        $permissions = [
            'can_view' => auth()->user()->can('financial-accounts.general.view'),
            'can_create' => auth()->user()->can('financial-accounts.general.create'),
            'can_edit' => auth()->user()->can('financial-accounts.general.edit'),
            'can_delete' => auth()->user()->can('financial-accounts.general.delete'),
        ];

        return view('backoffice.finance.accounts.index', compact('accounts', 'permissions'));
    }

    /**
     * Show the form for creating a new financial account.
     */
    public function create()
    {
        // ✅ Vérifier la permission CREATE
        if (!auth()->user()->can('financial-accounts.general.create')) {
            abort(403, 'Vous n\'avez pas la permission de créer des comptes financiers.');
        }

        return view('backoffice.finance.accounts.partials._modal_create');
    }

    /**
     * Store a newly created financial account.
     */
    public function store(FinancialAccountStoreRequest $request)
    {
        // ✅ Vérifier la permission CREATE
        if (!auth()->user()->can('financial-accounts.general.create')) {
            abort(403, 'Vous n\'avez pas la permission de créer des comptes financiers.');
        }

        try {
            DB::beginTransaction();

            $data = $request->validated();
            $data['agency_id'] = Auth::guard('backoffice')->user()->agency_id;
            $data['current_balance'] = $data['initial_balance'];

            // If this is set as default, remove default from other accounts
            if (!empty($data['is_default'])) {
                FinancialAccount::where('agency_id', $data['agency_id'])
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            }

            $account = FinancialAccount::create($data);
            
            $this->createNotification('store', 'financial-account', $account);
            
            DB::commit();

            return redirect()
                ->route('backoffice.finance.accounts.index')
                ->with('toast', [
                    'title' => 'Créé',
                    'message' => 'Compte financier créé avec succès.',
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
     * Display the specified financial account.
     */
    public function show(FinancialAccount $financialAccount)
    {
        // ✅ Vérifier la permission VIEW
        if (!auth()->user()->can('financial-accounts.general.view')) {
            abort(403, 'Vous n\'avez pas la permission de voir les comptes financiers.');
        }

        // Vérifier que le compte appartient à l'agence de l'utilisateur
        $agencyId = Auth::guard('backoffice')->user()->agency_id;
        if ($financialAccount->agency_id !== $agencyId) {
            abort(403, 'Ce compte n\'appartient pas à votre agence.');
        }

        $financialAccount->load(['transactions' => function($q) {
            $q->latest()->limit(10);
        }]);

        // ✅ Passer les permissions à la vue
        $permissions = [
            'can_edit' => auth()->user()->can('financial-accounts.general.edit'),
            'can_delete' => auth()->user()->can('financial-accounts.general.delete'),
        ];

        return view('backoffice.finance.accounts.show', compact('financialAccount', 'permissions'));
    }

    /**
     * Show the form for editing the specified financial account.
     */
    public function edit(FinancialAccount $financialAccount)
    {
        // ✅ Vérifier la permission EDIT
        if (!auth()->user()->can('financial-accounts.general.edit')) {
            abort(403, 'Vous n\'avez pas la permission de modifier les comptes financiers.');
        }

        // Vérifier que le compte appartient à l'agence de l'utilisateur
        $agencyId = Auth::guard('backoffice')->user()->agency_id;
        if ($financialAccount->agency_id !== $agencyId) {
            abort(403, 'Ce compte n\'appartient pas à votre agence.');
        }

        return view('backoffice.finance.accounts.partials._modal_edit', compact('financialAccount'));
    }

    /**
     * Update the specified financial account.
     */
    public function update(FinancialAccountUpdateRequest $request, FinancialAccount $financialAccount)
    {
        // ✅ Vérifier la permission EDIT
        if (!auth()->user()->can('financial-accounts.general.edit')) {
            abort(403, 'Vous n\'avez pas la permission de modifier les comptes financiers.');
        }

        // Vérifier que le compte appartient à l'agence de l'utilisateur
        $agencyId = Auth::guard('backoffice')->user()->agency_id;
        if ($financialAccount->agency_id !== $agencyId) {
            abort(403, 'Ce compte n\'appartient pas à votre agence.');
        }

        try {
            DB::beginTransaction();

            $data = $request->validated();

            // If this is set as default, remove default from other accounts
            if (!empty($data['is_default']) && !$financialAccount->is_default) {
                FinancialAccount::where('agency_id', $agencyId)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            }

            // Calculate balance difference
            $balanceDiff = $data['initial_balance'] - $financialAccount->initial_balance;
            $data['current_balance'] = $financialAccount->current_balance + $balanceDiff;

            $financialAccount->update($data);
            
            $this->createNotification('update', 'financial-account', $financialAccount);

            DB::commit();

            return redirect()
                ->route('backoffice.finance.accounts.show', $financialAccount)
                ->with('toast', [
                    'title' => 'Mis à jour',
                    'message' => 'Compte financier mis à jour avec succès.',
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
     * Remove the specified financial account.
     */
    public function destroy(FinancialAccount $financialAccount)
    {
        // ✅ Vérifier la permission DELETE
        if (!auth()->user()->can('financial-accounts.general.delete')) {
            abort(403, 'Vous n\'avez pas la permission de supprimer les comptes financiers.');
        }

        // Vérifier que le compte appartient à l'agence de l'utilisateur
        $agencyId = Auth::guard('backoffice')->user()->agency_id;
        if ($financialAccount->agency_id !== $agencyId) {
            abort(403, 'Ce compte n\'appartient pas à votre agence.');
        }

        try {
            DB::beginTransaction();

            // Check if account has transactions
            if ($financialAccount->transactions()->count() > 0) {
                return redirect()->back()->with('toast', [
                    'title' => 'Erreur',
                    'message' => 'Impossible de supprimer un compte qui a des transactions.',
                    'dot' => '#dc3545',
                    'delay' => 3500,
                    'time' => 'now',
                ]);
            }

            // Store account data for notification before delete
            $accountData = clone $financialAccount;
            $financialAccount->delete();
            
            $this->createNotification('destroy', 'financial-account', $accountData);

            DB::commit();

            return redirect()
                ->route('backoffice.finance.accounts.index')
                ->with('toast', [
                    'title' => 'Supprimé',
                    'message' => 'Compte financier supprimé avec succès.',
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