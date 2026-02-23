<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use App\Http\Requests\Backoffice\Vehicle\VehicleStoreRequest;
use App\Http\Requests\Backoffice\Vehicle\VehicleUpdateRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $agencyId = Auth::guard('backoffice')->user()->agency_id;

        $query = Vehicle::where('agency_id', $agencyId)
            ->with('model.brand');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('registration_number', 'like', "%{$search}%")
                  ->orWhere('vin', 'like', "%{$search}%")
                  ->orWhereHas('model', function ($sub) use ($search) {
                      $sub->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('model.brand', function ($sub) use ($search) {
                      $sub->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('select_cars')) {
            $query->where('vehicle_model_id', $request->select_cars);
        }

        if ($request->filled('type')) {
            $type = $request->type;
            $query->whereHas('model', function($q) use ($type) {
                $q->where('name', 'like', "%{$type}%")
                  ->orWhereHas('brand', function($sub) use ($type) {
                      $sub->where('name', 'like', "%{$type}%");
                  });
            });
        }

        if ($request->filled('location')) {
            $location = $request->location;
            $query->where('registration_city', 'like', "%{$location}%");
        }

        if ($request->filled('model_id')) {
            $query->where('vehicle_model_id', $request->model_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->get('sort') === 'az') {
            $query->orderBy('registration_number', 'asc');
        } elseif ($request->get('sort') === 'za') {
            $query->orderBy('registration_number', 'desc');
        } elseif ($request->get('sort') === 'price_asc') {
            $query->orderBy('daily_rate', 'asc');
        } elseif ($request->get('sort') === 'price_desc') {
            $query->orderBy('daily_rate', 'desc');
        } elseif ($request->get('sort') === 'mileage_asc') {
            $query->orderBy('current_mileage', 'asc');
        } elseif ($request->get('sort') === 'mileage_desc') {
            $query->orderBy('current_mileage', 'desc');
        } else {
            $query->latest();
        }

        $vehicles = $query->paginate(15)->withQueryString();
        
        $models = VehicleModel::where('agency_id', $agencyId)
            ->with('brand')
            ->orderBy('name')
            ->get();

        return view('backoffice.vehicles.index', compact('vehicles', 'models'));
    }

    public function create()
    {
        $models = VehicleModel::where('agency_id', Auth::guard('backoffice')->user()->agency_id)
            ->with('brand')
            ->orderBy('name')
            ->get();
        return view('backoffice.vehicles.partials._modal_create', compact('models'));
    }

    /**
     * Check if registration number already exists (AJAX)
     */
    public function checkDuplicate(Request $request)
    {
        $request->validate([
            'registration_number' => 'required|string|max:50'
        ]);

        $exists = Vehicle::where('agency_id', Auth::guard('backoffice')->user()->agency_id)
                        ->where('registration_number', $request->registration_number)
                        ->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'Ce numéro d\'immatriculation existe déjà.' : 'Numéro disponible'
        ]);
    }

    public function store(VehicleStoreRequest $request)
    {
        $data = $request->validated();
        $data['agency_id'] = Auth::guard('backoffice')->user()->agency_id;
        
        // Set boolean values for all 7 equipment options
        $data['has_gps'] = (bool) ($data['has_gps'] ?? false);
        $data['has_air_conditioning'] = (bool) ($data['has_air_conditioning'] ?? true);
        $data['has_bluetooth'] = (bool) ($data['has_bluetooth'] ?? false);
        $data['has_baby_seat'] = (bool) ($data['has_baby_seat'] ?? false); // Changed from has_usb
        $data['has_camera_recul'] = (bool) ($data['has_camera_recul'] ?? false);
        $data['has_regulateur_vitesse'] = (bool) ($data['has_regulateur_vitesse'] ?? false);
        $data['has_siege_chauffant'] = (bool) ($data['has_siege_chauffant'] ?? false);
        
        unset($data['photos'], $data['documents']);

        $vehicle = Vehicle::create($data);
        
        // FIXED: Use correct module name 'vehicle' and the actual vehicle object
        $this->createNotification('store', 'vehicle', $vehicle);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                if ($file) $vehicle->addMedia($file)->toMediaCollection('vehicle_photos');
            }
        }

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                if ($file) $vehicle->addMedia($file)->toMediaCollection('vehicle_documents');
            }
        }

        return redirect()
            ->route('backoffice.vehicles.index')
            ->with('toast', [
                'title'   => 'Créé',
                'message' => 'Véhicule créé avec succès.',
                'dot'     => '#198754',
                'delay'   => 3500,
                'time'    => 'now',
            ]);
    }

    public function show(Vehicle $vehicle)
    {
        $this->authorize('view', $vehicle);
        $vehicle->load('model.brand');
        return view('backoffice.vehicles.show', compact('vehicle'));
    }

    public function edit(Vehicle $vehicle)
    {
        $this->authorize('update', $vehicle);
        $models = VehicleModel::where('agency_id', Auth::guard('backoffice')->user()->agency_id)
            ->with('brand')
            ->orderBy('name')
            ->get();
        $vehicle->load('model.brand');
        return view('backoffice.vehicles.partials._modal_edit', compact('vehicle', 'models'));
    }

    public function update(VehicleUpdateRequest $request, Vehicle $vehicle)
    {
        $this->authorize('update', $vehicle);
        $data = $request->validated();
        
        // Set boolean values for all 7 equipment options
        $data['has_gps'] = (bool) ($data['has_gps'] ?? false);
        $data['has_air_conditioning'] = (bool) ($data['has_air_conditioning'] ?? true);
        $data['has_bluetooth'] = (bool) ($data['has_bluetooth'] ?? false);
        $data['has_baby_seat'] = (bool) ($data['has_baby_seat'] ?? false); // Changed from has_usb
        $data['has_camera_recul'] = (bool) ($data['has_camera_recul'] ?? false);
        $data['has_regulateur_vitesse'] = (bool) ($data['has_regulateur_vitesse'] ?? false);
        $data['has_siege_chauffant'] = (bool) ($data['has_siege_chauffant'] ?? false);
        
        unset($data['photos'], $data['documents']);

        $vehicle->update($data);
        
        // ADDED: Create notification for update
        $this->createNotification('update', 'vehicle', $vehicle);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                if ($file) $vehicle->addMedia($file)->toMediaCollection('vehicle_photos');
            }
        }

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                if ($file) $vehicle->addMedia($file)->toMediaCollection('vehicle_documents');
            }
        }

        return redirect()
            ->route('backoffice.vehicles.show', $vehicle)
            ->with('toast', [
                'title'   => 'Mis à jour',
                'message' => 'Véhicule mis à jour avec succès.',
                'dot'     => '#0d6efd',
                'delay'   => 3500,
                'time'    => 'now',
            ]);
    }

    public function destroy(Vehicle $vehicle)
    {
        $this->authorize('delete', $vehicle);
         $item->delete();
        // Store vehicle data for notification before delete
        $vehicleData = clone $vehicle;
        $vehicle->delete();
        
        // ADDED: Create notification for delete
        $this->createNotification('destroy', 'vehicle', $vehicleData);

        return redirect()
            ->route('backoffice.vehicles.index')
            ->with('toast', [
                'title'   => 'Supprimé',
                'message' => 'Véhicule supprimé avec succès.',
                'dot'     => '#dc3545',
                'delay'   => 3500,
                'time'    => 'now',
            ]);
    }
}