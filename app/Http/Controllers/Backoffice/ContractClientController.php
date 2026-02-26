<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\ContractClient;
use App\Models\RentalContract;
use App\Models\Client;
use App\Http\Requests\Backoffice\ContractClient\ContractClientStoreRequest;
use App\Http\Requests\Backoffice\ContractClient\ContractClientUpdateRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContractClientController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of all contract clients.
     */
    public function index(Request $request)
    {
        // ✅ Vérifier la permission VIEW
        if (!auth()->user()->can('contract-clients.general.view')) {
            abort(403, 'Vous n\'avez pas la permission de voir les relations client-contrat.');
        }

        $agencyId = Auth::guard('backoffice')->user()->agency_id;

        $query = ContractClient::with(['rentalContract', 'client'])
            ->whereHas('rentalContract', function($q) use ($agencyId) {
                $q->where('agency_id', $agencyId);
            });

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('client', function($sub) use ($search) {
                    $sub->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                })->orWhereHas('rentalContract', function($sub) use ($search) {
                    $sub->where('contract_number', 'like', "%{$search}%");
                });
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by contract
        if ($request->filled('contract_id')) {
            $query->where('rental_contract_id', $request->contract_id);
        }

        // Sort
        $sort = $request->get('sort', 'latest');
        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } elseif ($sort === 'order_asc') {
            $query->orderBy('order', 'asc');
        } elseif ($sort === 'order_desc') {
            $query->orderBy('order', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $contractClients = $query->paginate(15)->withQueryString();

        // Get contracts and clients for filters
        $contracts = RentalContract::where('agency_id', $agencyId)->orderBy('contract_number')->get();
        $clients = Client::where('agency_id', $agencyId)->orderBy('first_name')->get();

        // ✅ Passer les permissions à la vue
        $permissions = [
            'can_view' => auth()->user()->can('contract-clients.general.view'),
            'can_create' => auth()->user()->can('contract-clients.general.create'),
            'can_edit' => auth()->user()->can('contract-clients.general.edit'),
            'can_delete' => auth()->user()->can('contract-clients.general.delete'),
        ];

        return view('backoffice.contract-clients.index', compact('contractClients', 'contracts', 'clients', 'permissions'));
    }

    /**
     * Show the form for creating a new contract client.
     */
    public function create()
    {
        // ✅ Vérifier la permission CREATE
        if (!auth()->user()->can('contract-clients.general.create')) {
            abort(403, 'Vous n\'avez pas la permission de créer des relations client-contrat.');
        }

        $agencyId = Auth::guard('backoffice')->user()->agency_id;
        
        $contracts = RentalContract::where('agency_id', $agencyId)
            ->orderBy('contract_number')
            ->get();
            
        $clients = Client::where('agency_id', $agencyId)
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get();

        // Get the next order number (default to 1)
        $nextOrder = ContractClient::max('order') ?? 0;
        $nextOrder++;

        return view('backoffice.contract-clients.partials._modal_create', compact('contracts', 'clients', 'nextOrder'));
    }

    /**
     * Store a newly created contract client.
     */
    public function store(ContractClientStoreRequest $request)
    {
        // ✅ Vérifier la permission CREATE
        if (!auth()->user()->can('contract-clients.general.create')) {
            abort(403, 'Vous n\'avez pas la permission de créer des relations client-contrat.');
        }

        try {
            DB::beginTransaction();

            $data = $request->validated();

            // Check if this client is already attached to this contract
            $exists = ContractClient::where('rental_contract_id', $data['rental_contract_id'])
                ->where('client_id', $data['client_id'])
                ->exists();

            if ($exists) {
                return redirect()->back()->withInput()->with('toast', [
                    'title' => 'Erreur',
                    'message' => 'Ce client est déjà associé à ce contrat.',
                    'dot' => '#dc3545',
                    'delay' => 3500,
                    'time' => 'now',
                ]);
            }

            // Check if trying to add primary when one already exists
            if ($data['role'] === 'primary') {
                $primaryExists = ContractClient::where('rental_contract_id', $data['rental_contract_id'])
                    ->where('role', 'primary')
                    ->exists();

                if ($primaryExists) {
                    return redirect()->back()->withInput()->with('toast', [
                        'title' => 'Erreur',
                        'message' => 'Un client principal existe déjà pour ce contrat.',
                        'dot' => '#dc3545',
                        'delay' => 3500,
                        'time' => 'now',
                    ]);
                }
            }

            $contractClient = ContractClient::create($data);
            
            $this->createNotification('store', 'contract-client', $contractClient);

            DB::commit();

            return redirect()
                ->route('backoffice.contract-clients.index')
                ->with('toast', [
                    'title' => 'Créé',
                    'message' => 'Relation client-contrat créée avec succès.',
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
     * Display the specified contract client.
     */
    public function show(ContractClient $contractClient)
    {
        // ✅ Vérifier la permission VIEW
        if (!auth()->user()->can('contract-clients.general.view')) {
            abort(403, 'Vous n\'avez pas la permission de voir les relations client-contrat.');
        }

        $contractClient->load(['rentalContract', 'client']);

        // ✅ Passer les permissions à la vue
        $permissions = [
            'can_edit' => auth()->user()->can('contract-clients.general.edit'),
            'can_delete' => auth()->user()->can('contract-clients.general.delete'),
        ];

        return view('backoffice.contract-clients.show', compact('contractClient', 'permissions'));
    }

    /**
     * Show the form for editing the specified contract client.
     */
    public function edit(ContractClient $contractClient)
    {
        // ✅ Vérifier la permission EDIT
        if (!auth()->user()->can('contract-clients.general.edit')) {
            abort(403, 'Vous n\'avez pas la permission de modifier les relations client-contrat.');
        }

        $agencyId = Auth::guard('backoffice')->user()->agency_id;
        
        $contracts = RentalContract::where('agency_id', $agencyId)
            ->orderBy('contract_number')
            ->get();
            
        $clients = Client::where('agency_id', $agencyId)
            ->orderBy('first_name')
            ->get();

        return view('backoffice.contract-clients.partials._modal_edit', compact('contractClient', 'contracts', 'clients'));
    }

    /**
     * Update the specified contract client.
     */
    public function update(ContractClientUpdateRequest $request, ContractClient $contractClient)
    {
        // ✅ Vérifier la permission EDIT
        if (!auth()->user()->can('contract-clients.general.edit')) {
            abort(403, 'Vous n\'avez pas la permission de modifier les relations client-contrat.');
        }

        try {
            DB::beginTransaction();

            $data = $request->validated();

            // Check if trying to change to primary when one already exists (excluding itself)
            if ($data['role'] === 'primary' && $contractClient->role !== 'primary') {
                $primaryExists = ContractClient::where('rental_contract_id', $contractClient->rental_contract_id)
                    ->where('role', 'primary')
                    ->where('id', '!=', $contractClient->id)
                    ->exists();

                if ($primaryExists) {
                    return redirect()->back()->withInput()->with('toast', [
                        'title' => 'Erreur',
                        'message' => 'Un client principal existe déjà pour ce contrat.',
                        'dot' => '#dc3545',
                        'delay' => 3500,
                        'time' => 'now',
                    ]);
                }
            }

            $contractClient->update($data);
            
            $this->createNotification('update', 'contract-client', $contractClient);

            DB::commit();

            return redirect()
                ->route('backoffice.contract-clients.index')
                ->with('toast', [
                    'title' => 'Mis à jour',
                    'message' => 'Relation client-contrat mise à jour avec succès.',
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
     * Remove the specified contract client.
     */
    public function destroy(ContractClient $contractClient)
    {
        // ✅ Vérifier la permission DELETE
        if (!auth()->user()->can('contract-clients.general.delete')) {
            abort(403, 'Vous n\'avez pas la permission de supprimer les relations client-contrat.');
        }

        try {
            DB::beginTransaction();

            $contractClientData = clone $contractClient;
            $contractClient->delete();
            
            $this->createNotification('destroy', 'contract-client', $contractClientData);
            
            DB::commit();

            return redirect()
                ->route('backoffice.contract-clients.index')
                ->with('toast', [
                    'title' => 'Supprimé',
                    'message' => 'Relation client-contrat supprimée avec succès.',
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