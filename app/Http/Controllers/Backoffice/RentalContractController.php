<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\RentalContract;
use App\Models\Vehicle;
use App\Models\Client;
use App\Models\Agency;
use App\Http\Requests\Backoffice\RentalContract\RentalContractStoreRequest;
use App\Http\Requests\Backoffice\RentalContract\RentalContractUpdateRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RentalContractController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of rental contracts.
     */
    public function index(Request $request)
    {
        // ✅ Vérifier la permission VIEW
        if (!auth()->user()->can('rental-contracts.general.view')) {
            abort(403, 'Vous n\'avez pas la permission de voir les contrats.');
        }

        $agencyId = Auth::guard('backoffice')->user()->agency_id;

        $query = RentalContract::where('agency_id', $agencyId)
            ->with(['vehicle', 'primaryClient', 'secondaryClient', 'createdBy']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('contract_number', 'like', "%{$search}%")
                  ->orWhere('pickup_location', 'like', "%{$search}%")
                  ->orWhere('dropoff_location', 'like', "%{$search}%")
                  ->orWhereHas('vehicle', function ($sub) use ($search) {
                      $sub->where('registration_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('primaryClient', function ($sub) use ($search) {
                      $sub->where('first_name', 'like', "%{$search}%")
                           ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('end_date', '<=', $request->date_to);
        }

        // Filter by vehicle
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        // Filter by client
        if ($request->filled('client_id')) {
            $query->where('primary_client_id', $request->client_id);
        }

        // Sort
        $sort = $request->get('sort', 'latest');
        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } elseif ($sort === 'start_date_asc') {
            $query->orderBy('start_date', 'asc');
        } elseif ($sort === 'start_date_desc') {
            $query->orderBy('start_date', 'desc');
        } elseif ($sort === 'amount_asc') {
            $query->orderBy('total_amount', 'asc');
        } elseif ($sort === 'amount_desc') {
            $query->orderBy('total_amount', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $contracts = $query->paginate(15)->withQueryString();

        // Get vehicles and clients for filters
        $vehicles = Vehicle::where('agency_id', $agencyId)->orderBy('registration_number')->get();
        $clients = Client::where('agency_id', $agencyId)->orderBy('first_name')->get();

        // ✅ Passer les permissions à la vue
        $permissions = [
            'can_view' => auth()->user()->can('rental-contracts.general.view'),
            'can_create' => auth()->user()->can('rental-contracts.general.create'),
            'can_edit' => auth()->user()->can('rental-contracts.general.edit'),
            'can_delete' => auth()->user()->can('rental-contracts.general.delete'),
        ];

        return view('backoffice.rental-contracts.index', compact('contracts', 'vehicles', 'clients', 'permissions'));
    }

    /**
     * Generate a unique contract number
     */
    private function generateContractNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        $prefix = "CTR-{$year}{$month}-";
        
        // Get the maximum contract number with this prefix
        $maxContract = RentalContract::where('contract_number', 'like', $prefix . '%')
            ->orderByRaw('CAST(SUBSTRING(contract_number, -4) AS UNSIGNED) DESC')
            ->first();
        
        if ($maxContract) {
            // Extract the number from the last contract
            $lastNumber = intval(substr($maxContract->contract_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        $contractNumber = $prefix . $newNumber;
        
        // Double-check if it exists (safety)
        $attempts = 0;
        while (RentalContract::where('contract_number', $contractNumber)->exists() && $attempts < 100) {
            $attempts++;
            $newNumber = str_pad(intval($newNumber) + 1, 4, '0', STR_PAD_LEFT);
            $contractNumber = $prefix . $newNumber;
        }
        
        return $contractNumber;
    }

    /**
     * Show the form for creating a new contract.
     */
    public function create()
    {
        // ✅ Vérifier la permission CREATE
        if (!auth()->user()->can('rental-contracts.general.create')) {
            abort(403, 'Vous n\'avez pas la permission de créer des contrats.');
        }

        $agencyId = Auth::guard('backoffice')->user()->agency_id;
        
        $vehicles = Vehicle::where('agency_id', $agencyId)
            ->where('status', 'available')
            ->orderBy('registration_number')
            ->get();
            
        $clients = Client::where('agency_id', $agencyId)
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get();

        // Generate unique contract number
        $contractNumber = $this->generateContractNumber();

        return view('backoffice.rental-contracts.partials._modal_create', compact('vehicles', 'clients', 'contractNumber'));
    }

    /**
     * Store a newly created contract.
     */
    public function store(RentalContractStoreRequest $request)
    {
        // ✅ Vérifier la permission CREATE
        if (!auth()->user()->can('rental-contracts.general.create')) {
            abort(403, 'Vous n\'avez pas la permission de créer des contrats.');
        }

        try {
            DB::beginTransaction();

            $data = $request->validated();
            
            // Add agency and created_by
            $data['agency_id'] = Auth::guard('backoffice')->user()->agency_id;
            $data['created_by'] = Auth::guard('backoffice')->id();
            
            // Generate unique contract number (regenerate to be safe)
            $data['contract_number'] = $this->generateContractNumber();

            // Calculate dates
            $startDate = Carbon::parse($data['start_date']);
            $endDate = Carbon::parse($data['end_date']);
            
            // Calculate days
            $days = $startDate->diffInDays($endDate);
            $data['planned_days'] = $days > 0 ? $days : 1;
            
            // Calculate total amount
            $total = ($data['daily_rate'] * $data['planned_days']) - ($data['discount_amount'] ?? 0);
            $data['total_amount'] = max($total, 0);

            // Set datetime fields
            $data['start_at'] = Carbon::parse($data['start_date'] . ' ' . ($data['start_time'] ?? '00:00'));
            $data['end_at'] = Carbon::parse($data['end_date'] . ' ' . ($data['end_time'] ?? '23:59'));

            // Handle empty values
            $data['deposit_amount'] = !empty($data['deposit_amount']) ? $data['deposit_amount'] : null;
            $data['observations'] = !empty($data['observations']) ? $data['observations'] : null;
            $data['secondary_client_id'] = !empty($data['secondary_client_id']) ? $data['secondary_client_id'] : null;

            // Remove any fields that shouldn't be in the create
            unset($data['_token']);

            $contract = RentalContract::create($data);
            
            $this->createNotification('store', 'rental-contract', $contract);

            DB::commit();

            return redirect()
                ->route('backoffice.rental-contracts.show', $contract)
                ->with('toast', [
                    'title' => 'Créé',
                    'message' => 'Contrat de location créé avec succès.',
                    'dot' => '#198754',
                    'delay' => 3500,
                    'time' => 'now',
                ]);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            
            // Check if it's a duplicate entry error
            if ($e->errorInfo[1] == 1062) {
                // Try one more time with a new number
                try {
                    DB::beginTransaction();
                    
                    // Generate a timestamp-based number as fallback
                    $data['contract_number'] = 'CTR-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
                    
                    $contract = RentalContract::create($data);
                    
                    $this->createNotification('store', 'rental-contract', $contract);
                    
                    DB::commit();
                    
                    return redirect()
                        ->route('backoffice.rental-contracts.show', $contract)
                        ->with('toast', [
                            'title' => 'Créé',
                            'message' => 'Contrat de location créé avec succès.',
                            'dot' => '#198754',
                            'delay' => 3500,
                            'time' => 'now',
                        ]);
                } catch (\Exception $retryException) {
                    DB::rollBack();
                    return redirect()->back()->withInput()->with('toast', [
                        'title' => 'Erreur',
                        'message' => 'Erreur de duplication: ' . $e->getMessage(),
                        'dot' => '#dc3545',
                        'delay' => 3500,
                        'time' => 'now',
                    ]);
                }
            }
            
            return redirect()->back()->withInput()->with('toast', [
                'title' => 'Erreur',
                'message' => 'Erreur SQL: ' . $e->getMessage(),
                'dot' => '#dc3545',
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
     * Display the specified contract.
     */
    public function show(RentalContract $rentalContract)
    {
        // ✅ Vérifier la permission VIEW
        if (!auth()->user()->can('rental-contracts.general.view')) {
            abort(403, 'Vous n\'avez pas la permission de voir les contrats.');
        }
        
        $rentalContract->load(['vehicle', 'primaryClient', 'secondaryClient', 'createdBy', 'updatedBy', 'agency']);

        // ✅ Passer les permissions à la vue
        $permissions = [
            'can_edit' => auth()->user()->can('rental-contracts.general.edit'),
            'can_delete' => auth()->user()->can('rental-contracts.general.delete'),
        ];

        return view('backoffice.rental-contracts.show', compact('rentalContract', 'permissions'));
    }

    /**
     * Show the form for editing the specified contract.
     */
    public function edit(RentalContract $rentalContract)
    {
        // ✅ Vérifier la permission EDIT
        if (!auth()->user()->can('rental-contracts.general.edit')) {
            abort(403, 'Vous n\'avez pas la permission de modifier les contrats.');
        }
        
        $agencyId = Auth::guard('backoffice')->user()->agency_id;
        
        $vehicles = Vehicle::where('agency_id', $agencyId)->orderBy('registration_number')->get();
        $clients = Client::where('agency_id', $agencyId)->orderBy('first_name')->get();

        return view('backoffice.rental-contracts.partials._modal_edit', compact('rentalContract', 'vehicles', 'clients'));
    }

    /**
     * Update the specified contract.
     */
    public function update(RentalContractUpdateRequest $request, RentalContract $rentalContract)
    {
        // ✅ Vérifier la permission EDIT
        if (!auth()->user()->can('rental-contracts.general.edit')) {
            abort(403, 'Vous n\'avez pas la permission de modifier les contrats.');
        }

        try {
            DB::beginTransaction();

            $data = $request->validated();
            
            // Add updated_by
            $data['updated_by'] = Auth::guard('backoffice')->id();

            // Handle empty values
            $data['deposit_amount'] = !empty($data['deposit_amount']) ? $data['deposit_amount'] : null;
            $data['observations'] = !empty($data['observations']) ? $data['observations'] : null;
            $data['secondary_client_id'] = !empty($data['secondary_client_id']) ? $data['secondary_client_id'] : null;

            // Recalculate total amount if rates changed
            if (isset($data['daily_rate']) || isset($data['discount_amount']) || 
                isset($data['start_date']) || isset($data['end_date'])) {
                
                $startDate = isset($data['start_date']) ? Carbon::parse($data['start_date']) : Carbon::parse($rentalContract->start_date);
                $endDate = isset($data['end_date']) ? Carbon::parse($data['end_date']) : Carbon::parse($rentalContract->end_date);
                $dailyRate = $data['daily_rate'] ?? $rentalContract->daily_rate;
                $discount = $data['discount_amount'] ?? $rentalContract->discount_amount;

                $days = $startDate->diffInDays($endDate);
                $data['planned_days'] = $days > 0 ? $days : 1;
                
                $total = ($dailyRate * $data['planned_days']) - $discount;
                $data['total_amount'] = max($total, 0);

                // Update datetime fields
                $startTime = $data['start_time'] ?? $rentalContract->start_time;
                $endTime = $data['end_time'] ?? $rentalContract->end_time;
                
                $data['start_at'] = Carbon::parse($startDate->format('Y-m-d') . ' ' . ($startTime ?? '00:00'));
                $data['end_at'] = Carbon::parse($endDate->format('Y-m-d') . ' ' . ($endTime ?? '23:59'));
            }

            $rentalContract->update($data);
            
            $this->createNotification('update', 'rental-contract', $rentalContract);

            DB::commit();

            return redirect()
                ->route('backoffice.rental-contracts.show', $rentalContract)
                ->with('toast', [
                    'title' => 'Mis à jour',
                    'message' => 'Contrat mis à jour avec succès.',
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
     * Remove the specified contract.
     */
    public function destroy(RentalContract $rentalContract)
    {
        // ✅ Vérifier la permission DELETE
        if (!auth()->user()->can('rental-contracts.general.delete')) {
            abort(403, 'Vous n\'avez pas la permission de supprimer les contrats.');
        }

        try {
            DB::beginTransaction();
            
            $contractData = clone $rentalContract;
            $rentalContract->delete();
            
            $this->createNotification('destroy', 'rental-contract', $contractData);
            
            DB::commit();

            return redirect()
                ->route('backoffice.rental-contracts.index')
                ->with('toast', [
                    'title' => 'Supprimé',
                    'message' => 'Contrat supprimé avec succès.',
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
     * Update contract status.
     */
    public function updateStatus(Request $request, RentalContract $rentalContract)
    {
        // ✅ Vérifier la permission EDIT
        if (!auth()->user()->can('rental-contracts.general.edit')) {
            abort(403, 'Vous n\'avez pas la permission de modifier les contrats.');
        }

        $request->validate([
            'status' => ['required', 'in:draft,pending,accepted,in_progress,completed,cancelled'],
            'cancellation_reason' => 'required_if:status,cancelled|nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $data = ['status' => $request->status];
            
            if ($request->status === 'cancelled') {
                $data['cancelled_at'] = now();
                $data['cancellation_reason'] = $request->cancellation_reason;
            } elseif ($request->status === 'in_progress') {
                $data['actual_start_at'] = now();
            } elseif ($request->status === 'completed') {
                $data['actual_end_at'] = now();
            }

            $rentalContract->update($data);
            
            $this->createNotification('status', 'rental-contract', $rentalContract);

            DB::commit();

            return redirect()
                ->route('backoffice.rental-contracts.show', $rentalContract)
                ->with('toast', [
                    'title' => 'Statut mis à jour',
                    'message' => 'Le statut du contrat a été mis à jour.',
                    'dot' => '#0d6efd',
                    'delay' => 3500,
                    'time' => 'now',
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('toast', [
                'title' => 'Erreur',
                'message' => 'Erreur lors de la mise à jour du statut: ' . $e->getMessage(),
                'dot' => '#dc3545',
                'delay' => 3500,
                'time' => 'now',
            ]);
        }
    }
}