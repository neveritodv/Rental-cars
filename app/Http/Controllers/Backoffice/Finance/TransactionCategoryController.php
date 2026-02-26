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
use Illuminate\View\View;

class TransactionCategoryController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of transaction categories.
     */
    public function index(Request $request): View
    {
        // ✅ Vérifier la permission VIEW
        if (!auth()->user()->can('transaction-categories.general.view')) {
            abort(403, 'Vous n\'avez pas la permission de voir les catégories.');
        }

        $agencyId = Auth::guard('backoffice')->user()->agency_id;

        $query = TransactionCategory::where('agency_id', $agencyId)
            ->withCount('transactions')
            ->withSum('transactions as total_amount', 'amount');

        // 🔎 Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // 🏷️ Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // 🔤 Sort
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

        // ✅ Passer les permissions à la vue
        $permissions = [
            'can_view' => auth()->user()->can('transaction-categories.general.view'),
            'can_create' => auth()->user()->can('transaction-categories.general.create'),
            'can_edit' => auth()->user()->can('transaction-categories.general.edit'),
            'can_delete' => auth()->user()->can('transaction-categories.general.delete'),
        ];

        return view('backoffice.finance.categories.index', compact('categories', 'permissions'));
    }

    /**
     * Show the form for creating a new transaction category.
     */
    public function create()
    {
        // ✅ Vérifier la permission CREATE
        if (!auth()->user()->can('transaction-categories.general.create')) {
            abort(403, 'Vous n\'avez pas la permission de créer des catégories.');
        }

        return view('backoffice.finance.categories.partials._modal_create');
    }

    /**
     * Store a newly created transaction category.
     */
    public function store(TransactionCategoryStoreRequest $request)
    {
        // ✅ Vérifier la permission CREATE
        if (!auth()->user()->can('transaction-categories.general.create')) {
            abort(403, 'Vous n\'avez pas la permission de créer des catégories.');
        }

        try {
            DB::beginTransaction();

            $data = $request->validated();
            $data['agency_id'] = Auth::guard('backoffice')->user()->agency_id;

            $category = TransactionCategory::create($data);
            
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
        // ✅ Vérifier la permission VIEW
        if (!auth()->user()->can('transaction-categories.general.view')) {
            abort(403, 'Vous n\'avez pas la permission de voir les catégories.');
        }

        // Vérifier que la catégorie appartient à l'agence de l'utilisateur
        $agencyId = Auth::guard('backoffice')->user()->agency_id;
        if ($transactionCategory->agency_id !== $agencyId) {
            abort(403, 'Cette catégorie n\'appartient pas à votre agence.');
        }

        $transactionCategory->load(['transactions' => function($q) {
            $q->latest()->with('account')->limit(10);
        }]);

        $transactionCategory->loadCount('transactions');
        $transactionCategory->loadSum('transactions as total_amount', 'amount');

        // ✅ Passer les permissions à la vue
        $permissions = [
            'can_edit' => auth()->user()->can('transaction-categories.general.edit'),
            'can_delete' => auth()->user()->can('transaction-categories.general.delete'),
        ];

        return view('backoffice.finance.categories.show', compact('transactionCategory', 'permissions'));
    }

    /**
     * Show the form for editing the specified transaction category.
     */
    public function edit(TransactionCategory $transactionCategory)
    {
        // ✅ Vérifier la permission EDIT
        if (!auth()->user()->can('transaction-categories.general.edit')) {
            abort(403, 'Vous n\'avez pas la permission de modifier les catégories.');
        }

        // Vérifier que la catégorie appartient à l'agence de l'utilisateur
        $agencyId = Auth::guard('backoffice')->user()->agency_id;
        if ($transactionCategory->agency_id !== $agencyId) {
            abort(403, 'Cette catégorie n\'appartient pas à votre agence.');
        }

        return view('backoffice.finance.categories.partials._modal_edit', compact('transactionCategory'));
    }

    /**
     * Update the specified transaction category.
     */
    public function update(TransactionCategoryUpdateRequest $request, TransactionCategory $transactionCategory)
    {
        // ✅ Vérifier la permission EDIT
        if (!auth()->user()->can('transaction-categories.general.edit')) {
            abort(403, 'Vous n\'avez pas la permission de modifier les catégories.');
        }

        // Vérifier que la catégorie appartient à l'agence de l'utilisateur
        $agencyId = Auth::guard('backoffice')->user()->agency_id;
        if ($transactionCategory->agency_id !== $agencyId) {
            abort(403, 'Cette catégorie n\'appartient pas à votre agence.');
        }

        try {
            DB::beginTransaction();

            $transactionCategory->update($request->validated());
            
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
        // ✅ Vérifier la permission DELETE
        if (!auth()->user()->can('transaction-categories.general.delete')) {
            abort(403, 'Vous n\'avez pas la permission de supprimer les catégories.');
        }

        // Vérifier que la catégorie appartient à l'agence de l'utilisateur
        $agencyId = Auth::guard('backoffice')->user()->agency_id;
        if ($transactionCategory->agency_id !== $agencyId) {
            abort(403, 'Cette catégorie n\'appartient pas à votre agence.');
        }

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