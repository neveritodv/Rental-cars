<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backoffice\VehicleModel\VehicleModelStoreRequest;
use App\Http\Requests\Backoffice\VehicleModel\VehicleModelUpdateRequest;
use App\Models\VehicleBrand;
use App\Models\VehicleModel;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class VehicleModelController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $query = VehicleModel::where('agency_id', Auth::user()->agency_id)
            ->with('brand');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhereHas('brand', function($brandQuery) use ($search) {
                      $brandQuery->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        $models = $query->latest()->paginate(15);
        
        $brands = VehicleBrand::where('agency_id', Auth::user()->agency_id)
            ->orderBy('name')
            ->get();

        return view('backoffice.vehicle-models.index', compact('models', 'brands'));
    }

    public function create()
    {
        $vehicleModels = VehicleModel::where('agency_id', Auth::user()->agency_id)
            ->with('brand')
            ->orderBy('name')
            ->get();

        return view('backoffice.vehicles.create', compact('vehicleModels'));
    }

    public function store(VehicleModelStoreRequest $request)
    {
        $data = $request->validated();
        $data['agency_id'] = Auth::user()->agency_id;

        $model = VehicleModel::create($data);
        
        // FIXED: Use correct module name 'vehicle-model' and the actual model object
        $this->createNotification('store', 'vehicle-model', $model);

        return redirect()
            ->route('backoffice.vehicle-models.index')
            ->with('toast', [
                'title'   => 'Créé',
                'message' => 'Modèle de véhicule créé avec succès.',
                'dot'     => '#198754', // green
                'delay'   => 3500,
                'time'    => 'now',
            ]);
    }

    public function show(VehicleModel $vehicleModel)
    {
        $this->authorize('view', $vehicleModel);

        $vehicleModel->load('brand');

        return view('backoffice.vehicle-models.show', compact('vehicleModel'));
    }

    public function edit(VehicleModel $vehicleModel)
    {
        $this->authorize('update', $vehicleModel);

        $brands = VehicleBrand::where('agency_id', Auth::user()->agency_id)
            ->orderBy('name')
            ->get();

        return view('backoffice.vehicle-models.edit', compact('vehicleModel', 'brands'));
    }

    public function update(VehicleModelUpdateRequest $request, VehicleModel $vehicleModel)
    {
        $this->authorize('update', $vehicleModel);

        $vehicleModel->update([
            'name' => $request->name,
            'vehicle_brand_id' => $request->vehicle_brand_id,
            'is_active' => $request->status === 'active',
        ]);
        
        // ADDED: Create notification for update
        $this->createNotification('update', 'vehicle-model', $vehicleModel);

        return redirect()
            ->route('backoffice.vehicle-models.index')
            ->with('toast', [
                'title'   => 'Mis à jour',
                'message' => 'Modèle de véhicule mis à jour avec succès.',
                'dot'     => '#0d6efd', // blue
                'delay'   => 3500,
                'time'    => 'now',
            ]);
    }

    public function destroy(VehicleModel $vehicleModel)
    {
        $this->authorize('delete', $vehicleModel);
 $item->delete();
        // Store model data for notification before delete
        $modelData = clone $vehicleModel;
        $vehicleModel->delete();
        
        // ADDED: Create notification for delete
        $this->createNotification('destroy', 'vehicle-model', $modelData);

        return redirect()
            ->route('backoffice.vehicle-models.index')
            ->with('toast', [
                'title'   => 'Supprimé',
                'message' => 'Modèle de véhicule supprimé.',
                'dot'     => '#dc3545', // red
                'delay'   => 3500,
                'time'    => 'now',
            ]);
    }
}