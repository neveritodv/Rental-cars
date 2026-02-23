<?php

namespace App\Http\Controllers\Backoffice\Vehicles;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleOilChange;
use App\Http\Requests\Backoffice\VehicleOilChange\VehicleOilChangeStoreRequest;
use App\Http\Requests\Backoffice\VehicleOilChange\VehicleOilChangeUpdateRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class OilChangeController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the oil changes.
     * Route: /backoffice/vehicles/{vehicle}/oil-changes
     */
    public function index(Request $request, $vehicleId)
    {
        // ============ GLOBAL VIEW - ALL VEHICLES ============
        if ($vehicleId === 'all') {
            $query = VehicleOilChange::with('vehicle');
            
            // 🔎 SEARCH
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('mechanic_name', 'like', "%{$search}%")
                      ->orWhere('mileage', 'like', "%{$search}%")
                      ->orWhere('next_mileage', 'like', "%{$search}%")
                      ->orWhere('amount', 'like', "%{$search}%")
                      ->orWhere('observations', 'like', "%{$search}%")
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

            // 👨‍🔧 FILTER BY MECHANIC
            if ($request->filled('mechanic')) {
                $query->where('mechanic_name', 'like', "%{$request->mechanic}%");
            }

            // 🔢 FILTER BY MILEAGE RANGE
            if ($request->filled('mileage_min')) {
                $query->where('mileage', '>=', $request->mileage_min);
            }
            if ($request->filled('mileage_max')) {
                $query->where('mileage', '<=', $request->mileage_max);
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
            } elseif ($sort === 'mileage_asc') {
                $query->orderBy('mileage', 'asc');
            } elseif ($sort === 'mileage_desc') {
                $query->orderBy('mileage', 'desc');
            } elseif ($sort === 'amount_asc') {
                $query->orderBy('amount', 'asc');
            } elseif ($sort === 'amount_desc') {
                $query->orderBy('amount', 'desc');
            } else {
                $query->orderBy('date', 'desc');
            }

            $oilChanges = $query->paginate(15)->withQueryString();

            // Get available mechanics for filter
            $availableMechanics = VehicleOilChange::whereNotNull('mechanic_name')
                ->select('mechanic_name')
                ->distinct()
                ->orderBy('mechanic_name')
                ->pluck('mechanic_name');

            return view('Backoffice.oil-changes.index', [
                'vehicle' => null,
                'oilChanges' => $oilChanges,
                'availableMechanics' => $availableMechanics,
                'isGlobalView' => true
            ]);
        }
        
        // ============ SINGLE VEHICLE VIEW ============
        $vehicle = Vehicle::find($vehicleId);
        
        if (!$vehicle) {
            return view('Backoffice.oil-changes.index', [
                'vehicle' => null,
                'oilChanges' => new LengthAwarePaginator([], 0, 15),
                'availableMechanics' => collect([]),
                'isGlobalView' => false
            ]);
        }
        
        $this->authorize('view', $vehicle);

        $query = $vehicle->oilChanges();

        // 🔎 SEARCH
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('mechanic_name', 'like', "%{$search}%")
                  ->orWhere('mileage', 'like', "%{$search}%")
                  ->orWhere('next_mileage', 'like', "%{$search}%")
                  ->orWhere('amount', 'like', "%{$search}%")
                  ->orWhere('observations', 'like', "%{$search}%");
            });
        }

        // 📅 FILTER BY DATE RANGE
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        // 👨‍🔧 FILTER BY MECHANIC
        if ($request->filled('mechanic')) {
            $query->where('mechanic_name', 'like', "%{$request->mechanic}%");
        }

        // 🔢 FILTER BY MILEAGE RANGE
        if ($request->filled('mileage_min')) {
            $query->where('mileage', '>=', $request->mileage_min);
        }
        if ($request->filled('mileage_max')) {
            $query->where('mileage', '<=', $request->mileage_max);
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
        } elseif ($sort === 'mileage_asc') {
            $query->orderBy('mileage', 'asc');
        } elseif ($sort === 'mileage_desc') {
            $query->orderBy('mileage', 'desc');
        } elseif ($sort === 'amount_asc') {
            $query->orderBy('amount', 'asc');
        } elseif ($sort === 'amount_desc') {
            $query->orderBy('amount', 'desc');
        } else {
            $query->orderBy('date', 'desc');
        }

        $oilChanges = $query->paginate(15)->withQueryString();

        // Get available mechanics for filter
        $availableMechanics = VehicleOilChange::where('vehicle_id', $vehicle->id)
            ->whereNotNull('mechanic_name')
            ->select('mechanic_name')
            ->distinct()
            ->orderBy('mechanic_name')
            ->pluck('mechanic_name');

        return view('Backoffice.oil-changes.index', compact('vehicle', 'oilChanges', 'availableMechanics'));
    }

    public function create(Vehicle $vehicle = null)
    {
        $vehicles = Vehicle::orderBy('registration_number')->get();
        return view('Backoffice.oil-changes.partials._modal_create', compact('vehicle', 'vehicles'));
    }

    public function store(VehicleOilChangeStoreRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->validated();
            $vehicle = Vehicle::findOrFail($data['vehicle_id']);
            
            $oilChange = VehicleOilChange::create([
                'vehicle_id' => $vehicle->id,
                'date' => $data['date'],
                'amount' => $data['amount'],
                'mileage' => $data['mileage'],
                'next_mileage' => $data['next_mileage'],
                'mechanic_name' => $data['mechanic_name'] ?? null,
                'observations' => $data['observations'] ?? null,
            ]);
            
            // FIXED: Use correct module name 'oil-change' and the actual oilChange object
            $this->createNotification('store', 'oil-change', $oilChange);
            
            DB::commit();
            
            return redirect()->route('backoffice.vehicles.oil-changes.index', $vehicle->id)
                ->with('toast', [
                    'title' => 'Créé', 
                    'message' => 'Vidange créée avec succès.', 
                    'dot' => '#198754', 
                    'delay' => 3500, 
                    'time' => 'now'
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('toast', [
                'title' => 'Erreur', 
                'message' => 'Erreur lors de la création: ' . $e->getMessage(),
                'dot' => '#dc3545', 
                'delay' => 3500, 
                'time' => 'now'
            ]);
        }
    }

    public function show(Vehicle $vehicle, VehicleOilChange $oilChange)
    {
        $this->authorize('view', $vehicle);
        $this->verifyResource($vehicle, $oilChange);
        return view('Backoffice.oil-changes.show', compact('vehicle', 'oilChange'));
    }

    public function edit(Vehicle $vehicle, VehicleOilChange $oilChange)
    {
        $this->authorize('update', $vehicle);
        $this->verifyResource($vehicle, $oilChange);
        $vehicles = Vehicle::orderBy('registration_number')->get();
        return view('Backoffice.oil-changes.partials._modal_edit', compact('vehicle', 'oilChange', 'vehicles'));
    }

    public function update(VehicleOilChangeUpdateRequest $request, Vehicle $vehicle, VehicleOilChange $oilChange)
    {
        $this->authorize('update', $vehicle);
        $this->verifyResource($vehicle, $oilChange);

        try {
            DB::beginTransaction();
            $data = $request->validated();
            
            $oilChange->update([
                'vehicle_id' => $data['vehicle_id'],
                'date' => $data['date'],
                'amount' => $data['amount'],
                'mileage' => $data['mileage'],
                'next_mileage' => $data['next_mileage'],
                'mechanic_name' => $data['mechanic_name'] ?? null,
                'observations' => $data['observations'] ?? null,
            ]);
            
            // ADDED: Create notification for update
            $this->createNotification('update', 'oil-change', $oilChange);

            DB::commit();
            
            return redirect()->route('backoffice.vehicles.oil-changes.index', $oilChange->vehicle_id)
                ->with('toast', [
                    'title' => 'Mis à jour', 
                    'message' => 'Vidange mise à jour avec succès.', 
                    'dot' => '#0d6efd', 
                    'delay' => 3500, 
                    'time' => 'now'
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('toast', [
                'title' => 'Erreur', 
                'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage(),
                'dot' => '#dc3545', 
                'delay' => 3500, 
                'time' => 'now'
            ]);
        }
    }

    /**
     * Remove the specified oil change from storage.
     * Route: /backoffice/vehicles/{vehicle}/oil-changes/{oilChange} (DELETE)
     */
    public function destroy(Request $request, $vehicleId, VehicleOilChange $oilChange)
    {
        // Verify the oil change belongs to the vehicle
        if ($oilChange->vehicle_id != $vehicleId) {
            abort(404);
        }
        
        //$this->authorize('delete', $oilChange->vehicle);
        
        try {
            DB::beginTransaction();
            
            // Store oil change data for notification before delete
            $oilChangeData = clone $oilChange;
            $vehicleId = $oilChange->vehicle_id;
            $oilChange->delete();
            
            // ADDED: Create notification for delete
            $this->createNotification('destroy', 'oil-change', $oilChangeData);
            
            DB::commit();
            
            // Check if we came from a global view (all vehicles)
            $referer = $request->header('referer');
            $isGlobalView = str_contains($referer, '/vehicles/all/');
            
            // Smart redirect - go back to same view
            if ($isGlobalView) {
                return redirect()
                    ->route('backoffice.vehicles.oil-changes.index', ['vehicle' => 'all'])
                    ->with('toast', [
                        'title' => 'Supprimé',
                        'message' => 'Vidange supprimée avec succès.',
                        'dot' => '#dc3545',
                        'delay' => 3500,
                        'time' => 'now',
                    ]);
            } else {
                return redirect()
                    ->route('backoffice.vehicles.oil-changes.index', ['vehicle' => $vehicleId])
                    ->with('toast', [
                        'title' => 'Supprimé',
                        'message' => 'Vidange supprimée avec succès.',
                        'dot' => '#dc3545',
                        'delay' => 3500,
                        'time' => 'now',
                    ]);
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('toast', [
                    'title' => 'Erreur',
                    'message' => 'Erreur lors de la suppression: ' . $e->getMessage(),
                    'dot' => '#dc3545',
                    'delay' => 3500,
                    'time' => 'now',
                ]);
        }
    }

    private function verifyResource(Vehicle $vehicle, VehicleOilChange $oilChange): void
    {
        if ($oilChange->vehicle_id !== $vehicle->id) abort(404);
    }
}