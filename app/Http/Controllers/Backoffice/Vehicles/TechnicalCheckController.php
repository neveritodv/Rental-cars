<?php

namespace App\Http\Controllers\Backoffice\Vehicles;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleTechnicalCheck;
use App\Http\Requests\Backoffice\VehicleTechnicalCheck\VehicleTechnicalCheckStoreRequest;
use App\Http\Requests\Backoffice\VehicleTechnicalCheck\VehicleTechnicalCheckUpdateRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class TechnicalCheckController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the technical checks.
     * Route: /backoffice/vehicles/{vehicle}/technical-checks
     */
    public function index(Request $request, $vehicleId)
    {
        // ============ GLOBAL VIEW - ALL VEHICLES ============
        if ($vehicleId === 'all') {
            $query = VehicleTechnicalCheck::with('vehicle');
            
            // 🔎 SEARCH
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('date', 'like', "%{$search}%")
                      ->orWhere('amount', 'like', "%{$search}%")
                      ->orWhere('next_check_date', 'like', "%{$search}%")
                      ->orWhere('notes', 'like', "%{$search}%")
                      ->orWhereHas('vehicle', function ($sub) use ($search) {
                          $sub->where('registration_number', 'like', "%{$search}%")
                              ->orWhere('registration_city', 'like', "%{$search}%");
                      });
                });
            }

            // 📅 FILTER BY DATE RANGE
            if ($request->filled('date_from')) {
                $query->whereDate('date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('date', '<=', $request->date_to);
            }

            // 📅 FILTER BY NEXT CHECK DATE RANGE
            if ($request->filled('next_date_from')) {
                $query->whereDate('next_check_date', '>=', $request->next_date_from);
            }
            if ($request->filled('next_date_to')) {
                $query->whereDate('next_check_date', '<=', $request->next_date_to);
            }

            // 💰 FILTER BY AMOUNT RANGE
            if ($request->filled('amount_min')) {
                $query->where('amount', '>=', $request->amount_min);
            }
            if ($request->filled('amount_max')) {
                $query->where('amount', '<=', $request->amount_max);
            }

            // 🔤 SORT
            $sort = $request->get('sort', 'latest');
            if ($sort === 'oldest') {
                $query->orderBy('date', 'asc');
            } elseif ($sort === 'amount_asc') {
                $query->orderBy('amount', 'asc');
            } elseif ($sort === 'amount_desc') {
                $query->orderBy('amount', 'desc');
            } elseif ($sort === 'next_date_asc') {
                $query->orderBy('next_check_date', 'asc');
            } elseif ($sort === 'next_date_desc') {
                $query->orderBy('next_check_date', 'desc');
            } else {
                $query->orderBy('date', 'desc');
            }

            $technicalChecks = $query->paginate(15)->withQueryString();

            return view('Backoffice.technical-checks.index', [
                'vehicle' => null,
                'technicalChecks' => $technicalChecks,
                'availableYears' => collect([]),
                'isGlobalView' => true
            ]);
        }
        
        // ============ SINGLE VEHICLE VIEW ============
        $vehicle = Vehicle::find($vehicleId);
        
        if (!$vehicle) {
            return view('Backoffice.technical-checks.index', [
                'vehicle' => null,
                'technicalChecks' => new LengthAwarePaginator([], 0, 15),
                'availableYears' => collect([]),
                'isGlobalView' => false
            ]);
        }
        
        $this->authorize('view', $vehicle);

        $query = $vehicle->technicalChecks();

        // 🔎 SEARCH
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('date', 'like', "%{$search}%")
                  ->orWhere('amount', 'like', "%{$search}%")
                  ->orWhere('next_check_date', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // 📅 FILTER BY DATE RANGE
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        // 📅 FILTER BY NEXT CHECK DATE RANGE
        if ($request->filled('next_date_from')) {
            $query->whereDate('next_check_date', '>=', $request->next_date_from);
        }
        if ($request->filled('next_date_to')) {
            $query->whereDate('next_check_date', '<=', $request->next_date_to);
        }

        // 💰 FILTER BY AMOUNT RANGE
        if ($request->filled('amount_min')) {
            $query->where('amount', '>=', $request->amount_min);
        }
        if ($request->filled('amount_max')) {
            $query->where('amount', '<=', $request->amount_max);
        }

        // 🔤 SORT
        $sort = $request->get('sort', 'latest');
        
        if ($sort === 'oldest') {
            $query->orderBy('date', 'asc');
        } elseif ($sort === 'amount_asc') {
            $query->orderBy('amount', 'asc');
        } elseif ($sort === 'amount_desc') {
            $query->orderBy('amount', 'desc');
        } elseif ($sort === 'next_date_asc') {
            $query->orderBy('next_check_date', 'asc');
        } elseif ($sort === 'next_date_desc') {
            $query->orderBy('next_check_date', 'desc');
        } else {
            $query->orderBy('date', 'desc');
        }

        $technicalChecks = $query->paginate(15)->withQueryString();

        // Get available years for filter
        $availableYears = $vehicle ? VehicleTechnicalCheck::where('vehicle_id', $vehicle->id)
            ->selectRaw('YEAR(date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year') : collect([]);

        return view('Backoffice.technical-checks.index', compact('vehicle', 'technicalChecks', 'availableYears'));
    }

    /**
     * Show the form for creating a new technical check.
     * Route: /backoffice/vehicles/technical-checks/create
     */
    public function create(Vehicle $vehicle = null)
    {
        $vehicles = Vehicle::orderBy('registration_number')->get();
        return view('Backoffice.technical-checks.partials._modal_create', compact('vehicle', 'vehicles'));
    }

    /**
     * Store a newly created technical check in storage.
     * Route: /backoffice/vehicles/technical-checks (POST)
     */
    public function store(VehicleTechnicalCheckStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $vehicle = Vehicle::findOrFail($data['vehicle_id']);
            
            $technicalCheck = VehicleTechnicalCheck::create([
                'vehicle_id' => $vehicle->id,
                'date' => $data['date'],
                'amount' => $data['amount'],
                'next_check_date' => $data['next_check_date'],
                'notes' => $data['notes'] ?? null,
            ]);
            
            // FIXED: Use correct module name 'technical-check' and the actual technicalCheck object
            $this->createNotification('store', 'technical-check', $technicalCheck);
            
            DB::commit();

            return redirect()
                ->route('backoffice.vehicles.technical-checks.index', $vehicle->id)
                ->with('toast', [
                    'title' => 'Créé',
                    'message' => 'Contrôle technique créé avec succès.',
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
     * Display the specified technical check.
     * Route: /backoffice/vehicles/{vehicle}/technical-checks/{technicalCheck}
     */
    public function show(Vehicle $vehicle, VehicleTechnicalCheck $technicalCheck)
    {
        $this->authorize('view', $vehicle);
        $this->verifyResource($vehicle, $technicalCheck);
        return view('Backoffice.technical-checks.show', compact('vehicle', 'technicalCheck'));
    }

    /**
     * Show the form for editing the specified technical check.
     * Route: /backoffice/vehicles/{vehicle}/technical-checks/{technicalCheck}/edit
     */
    public function edit(Vehicle $vehicle, VehicleTechnicalCheck $technicalCheck)
    {
        $this->authorize('update', $vehicle);
        $this->verifyResource($vehicle, $technicalCheck);
        $vehicles = Vehicle::orderBy('registration_number')->get();
        return view('Backoffice.technical-checks.partials._modal_edit', compact('vehicle', 'technicalCheck', 'vehicles'));
    }

    /**
     * Update the specified technical check in storage.
     * Route: /backoffice/vehicles/{vehicle}/technical-checks/{technicalCheck} (PUT)
     */
    public function update(VehicleTechnicalCheckUpdateRequest $request, Vehicle $vehicle, VehicleTechnicalCheck $technicalCheck)
    {
        $this->authorize('update', $vehicle);
        $this->verifyResource($vehicle, $technicalCheck);

        try {
            DB::beginTransaction();
            $data = $request->validated();
            
            $technicalCheck->update([
                'vehicle_id' => $data['vehicle_id'],
                'date' => $data['date'],
                'amount' => $data['amount'],
                'next_check_date' => $data['next_check_date'],
                'notes' => $data['notes'] ?? null,
            ]);
            
            // ADDED: Create notification for update
            $this->createNotification('update', 'technical-check', $technicalCheck);

            DB::commit();
            
            return redirect()
                ->route('backoffice.vehicles.technical-checks.index', $technicalCheck->vehicle_id)
                ->with('toast', [
                    'title' => 'Mis à jour',
                    'message' => 'Contrôle technique mis à jour avec succès.',
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
     * Remove the specified technical check from storage.
     * Route: /backoffice/vehicles/{vehicle}/technical-checks/{technicalCheck} (DELETE)
     */
    public function destroy(Request $request, $vehicleId, VehicleTechnicalCheck $technicalCheck)
    {
        if ($technicalCheck->vehicle_id != $vehicleId) {
            abort(404);
        }
        
        //$this->authorize('delete', $technicalCheck->vehicle);
        
        try {
            DB::beginTransaction();
            
            // Store technical check data for notification before delete
            $technicalCheckData = clone $technicalCheck;
            $technicalCheck->delete();
            
            // ADDED: Create notification for delete
            $this->createNotification('destroy', 'technical-check', $technicalCheckData);
            
            DB::commit();
            
            $referer = $request->header('referer');
            $isGlobalView = str_contains($referer, '/vehicles/all/');
            
            if ($isGlobalView) {
                return redirect()
                    ->route('backoffice.vehicles.technical-checks.index', ['vehicle' => 'all'])
                    ->with('toast', [
                        'title' => 'Supprimé', 
                        'message' => 'Contrôle technique supprimé avec succès.', 
                        'dot' => '#dc3545'
                    ]);
            } else {
                return redirect()
                    ->route('backoffice.vehicles.technical-checks.index', ['vehicle' => $vehicleId])
                    ->with('toast', [
                        'title' => 'Supprimé', 
                        'message' => 'Contrôle technique supprimé avec succès.', 
                        'dot' => '#dc3545'
                    ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('toast', [
                'title' => 'Erreur', 
                'message' => 'Erreur lors de la suppression', 
                'dot' => '#dc3545'
            ]);
        }
    }

    /**
     * Verify that the technical check belongs to the vehicle.
     */
    private function verifyResource(Vehicle $vehicle, VehicleTechnicalCheck $technicalCheck): void
    {
        if ($technicalCheck->vehicle_id !== $vehicle->id) {
            abort(404);
        }
    }
}