<?php

namespace App\Http\Controllers\Backoffice\Vehicles;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleInsurance;
use App\Http\Requests\Backoffice\VehicleInsurance\VehicleInsuranceStoreRequest;
use App\Http\Requests\Backoffice\VehicleInsurance\VehicleInsuranceUpdateRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class InsuranceController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the insurances.
     * Route: /backoffice/vehicles/{vehicle}/insurances
     */
    public function index(Request $request, $vehicleId)
    {
        // ============ GLOBAL VIEW - ALL VEHICLES ============
        if ($vehicleId === 'all') {
            $query = VehicleInsurance::with('vehicle');
            
            // 🔎 SEARCH
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('company_name', 'like', "%{$search}%")
                      ->orWhere('policy_number', 'like', "%{$search}%")
                      ->orWhere('amount', 'like', "%{$search}%")
                      ->orWhere('notes', 'like', "%{$search}%")
                      ->orWhereHas('vehicle', function ($sub) use ($search) {
                          $sub->where('registration_number', 'like', "%{$search}%")
                              ->orWhere('registration_city', 'like', "%{$search}%");
                      });
                });
            }

            // 🏢 FILTER BY COMPANY
            if ($request->filled('company')) {
                $query->where('company_name', 'like', "%{$request->company}%");
            }

            // 📅 FILTER BY DATE RANGE
            if ($request->filled('date_from')) {
                $query->whereDate('date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('date', '<=', $request->date_to);
            }

            // 📅 FILTER BY NEXT INSURANCE DATE RANGE
            if ($request->filled('next_date_from')) {
                $query->whereDate('next_insurance_date', '>=', $request->next_date_from);
            }
            if ($request->filled('next_date_to')) {
                $query->whereDate('next_insurance_date', '<=', $request->next_date_to);
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
                $query->orderBy('next_insurance_date', 'asc');
            } elseif ($sort === 'next_date_desc') {
                $query->orderBy('next_insurance_date', 'desc');
            } else {
                $query->orderBy('date', 'desc');
            }

            $insurances = $query->paginate(15)->withQueryString();

            // Get available companies for filter
            $availableCompanies = VehicleInsurance::whereNotNull('company_name')
                ->select('company_name')
                ->distinct()
                ->orderBy('company_name')
                ->pluck('company_name');

            return view('Backoffice.insurances.index', [
                'vehicle' => null,
                'insurances' => $insurances,
                'availableCompanies' => $availableCompanies,
                'isGlobalView' => true
            ]);
        }
        
        // ============ SINGLE VEHICLE VIEW ============
        $vehicle = Vehicle::find($vehicleId);
        
        if (!$vehicle) {
            return view('Backoffice.insurances.index', [
                'vehicle' => null,
                'insurances' => new LengthAwarePaginator([], 0, 15),
                'availableCompanies' => collect([]),
                'isGlobalView' => false
            ]);
        }
        
        $this->authorize('view', $vehicle);

        $query = $vehicle->insurances();

        // 🔎 SEARCH
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('policy_number', 'like', "%{$search}%")
                  ->orWhere('amount', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // 🏢 FILTER BY COMPANY
        if ($request->filled('company')) {
            $query->where('company_name', 'like', "%{$request->company}%");
        }

        // 📅 FILTER BY DATE RANGE
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        // 📅 FILTER BY NEXT INSURANCE DATE RANGE
        if ($request->filled('next_date_from')) {
            $query->whereDate('next_insurance_date', '>=', $request->next_date_from);
        }
        if ($request->filled('next_date_to')) {
            $query->whereDate('next_insurance_date', '<=', $request->next_date_to);
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
            $query->orderBy('next_insurance_date', 'asc');
        } elseif ($sort === 'next_date_desc') {
            $query->orderBy('next_insurance_date', 'desc');
        } else {
            $query->orderBy('date', 'desc');
        }

        $insurances = $query->paginate(15)->withQueryString();

        // Get available companies for filter
        $availableCompanies = VehicleInsurance::where('vehicle_id', $vehicle->id)
            ->whereNotNull('company_name')
            ->select('company_name')
            ->distinct()
            ->orderBy('company_name')
            ->pluck('company_name');

        return view('Backoffice.insurances.index', compact('vehicle', 'insurances', 'availableCompanies'));
    }

    public function create(Vehicle $vehicle = null)
    {
        $vehicles = Vehicle::orderBy('registration_number')->get();
        return view('Backoffice.insurances.partials._modal_create', compact('vehicle', 'vehicles'));
    }

    public function store(VehicleInsuranceStoreRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->validated();
            $vehicle = Vehicle::findOrFail($data['vehicle_id']);
            
            $insurance = VehicleInsurance::create([
                'vehicle_id' => $vehicle->id,
                'company_name' => $data['company_name'] ?? null,
                'policy_number' => $data['policy_number'] ?? null,
                'date' => $data['date'],
                'amount' => $data['amount'],
                'next_insurance_date' => $data['next_insurance_date'],
                'notes' => $data['notes'] ?? null,
            ]);
            
            // FIXED: Use correct module name 'insurance' and the actual insurance object
            $this->createNotification('store', 'insurance', $insurance);
            
            DB::commit();
            
            return redirect()->route('backoffice.vehicles.insurances.index', $vehicle->id)
                ->with('toast', [
                    'title' => 'Créé', 
                    'message' => 'Assurance créée avec succès.', 
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

    public function show(Vehicle $vehicle, VehicleInsurance $insurance)
    {
        $this->authorize('view', $vehicle);
        $this->verifyResource($vehicle, $insurance);
        return view('Backoffice.insurances.show', compact('vehicle', 'insurance'));
    }

    public function edit(Vehicle $vehicle, VehicleInsurance $insurance)
    {
        $this->authorize('update', $vehicle);
        $this->verifyResource($vehicle, $insurance);
        $vehicles = Vehicle::orderBy('registration_number')->get();
        return view('Backoffice.insurances.partials._modal_edit', compact('vehicle', 'insurance', 'vehicles'));
    }

    public function update(VehicleInsuranceUpdateRequest $request, Vehicle $vehicle, VehicleInsurance $insurance)
    {
        $this->authorize('update', $vehicle);
        $this->verifyResource($vehicle, $insurance);

        try {
            DB::beginTransaction();
            $data = $request->validated();
            
            $insurance->update([
                'vehicle_id' => $data['vehicle_id'],
                'company_name' => $data['company_name'] ?? null,
                'policy_number' => $data['policy_number'] ?? null,
                'date' => $data['date'],
                'amount' => $data['amount'],
                'next_insurance_date' => $data['next_insurance_date'],
                'notes' => $data['notes'] ?? null,
            ]);
            
            // ADDED: Create notification for update
            $this->createNotification('update', 'insurance', $insurance);

            DB::commit();
            
            return redirect()->route('backoffice.vehicles.insurances.index', $insurance->vehicle_id)
                ->with('toast', [
                    'title' => 'Mis à jour', 
                    'message' => 'Assurance mise à jour avec succès.', 
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

    public function destroy(Request $request, $vehicleId, VehicleInsurance $insurance)
    {
        if ($insurance->vehicle_id != $vehicleId) {
            abort(404);
        }
        
        //$this->authorize('delete', $insurance->vehicle);
        
        try {
            DB::beginTransaction();
            
            // Store insurance data for notification before delete
            $insuranceData = clone $insurance;
            $insurance->delete();
            
            // ADDED: Create notification for delete
            $this->createNotification('destroy', 'insurance', $insuranceData);
            
            DB::commit();
            
            $referer = $request->header('referer');
            $isGlobalView = str_contains($referer, '/vehicles/all/');
            
            if ($isGlobalView) {
                return redirect()
                    ->route('backoffice.vehicles.insurances.index', ['vehicle' => 'all'])
                    ->with('toast', [
                        'title' => 'Supprimé', 
                        'message' => 'Assurance supprimée avec succès.', 
                        'dot' => '#dc3545'
                    ]);
            } else {
                return redirect()
                    ->route('backoffice.vehicles.insurances.index', ['vehicle' => $vehicleId])
                    ->with('toast', [
                        'title' => 'Supprimé', 
                        'message' => 'Assurance supprimée avec succès.', 
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

    private function verifyResource(Vehicle $vehicle, VehicleInsurance $insurance): void
    {
        if ($insurance->vehicle_id !== $vehicle->id) abort(404);
    }
}