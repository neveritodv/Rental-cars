<?php

namespace App\Http\Controllers\Backoffice\Vehicles;

use App\Http\Controllers\Controller;
use App\Models\VehicleControlItem;
use App\Models\VehicleControl;
use App\Http\Requests\Backoffice\VehicleControlItem\VehicleControlItemStoreRequest;
use App\Http\Requests\Backoffice\VehicleControlItem\VehicleControlItemUpdateRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ControlItemController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of vehicle control items.
     */
    public function index(Request $request)
    {
        $agencyId = Auth::guard('backoffice')->user()->agency_id;

        $query = VehicleControlItem::with(['vehicleControl' => function($q) use ($agencyId) {
                $q->where('agency_id', $agencyId);
            }])
            ->whereHas('vehicleControl', function($q) use ($agencyId) {
                $q->where('agency_id', $agencyId);
            });

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('item_key', 'like', "%{$search}%")
                  ->orWhere('label', 'like', "%{$search}%")
                  ->orWhere('comment', 'like', "%{$search}%");
            });
        }

        // Filter by control
        if ($request->filled('control_id')) {
            $query->where('vehicle_control_id', $request->control_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sort
        $sort = $request->get('sort', 'latest');
        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } elseif ($sort === 'key_asc') {
            $query->orderBy('item_key', 'asc');
        } elseif ($sort === 'key_desc') {
            $query->orderBy('item_key', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $items = $query->paginate(15)->withQueryString();

        // Get controls for filter dropdown
        $controls = VehicleControl::where('agency_id', $agencyId)
            ->orderBy('control_number')
            ->get();

        return view('Backoffice.control-items.index', compact('items', 'controls'));
    }

    /**
     * Show the form for creating a new vehicle control item.
     */
    public function create(Request $request)
    {
        $agencyId = Auth::guard('backoffice')->user()->agency_id;
        
        $controls = VehicleControl::where('agency_id', $agencyId)
            ->orderBy('control_number')
            ->get();

        $selectedControlId = $request->query('control_id');

        return view('Backoffice.control-items.partials._modal_create', compact('controls', 'selectedControlId'));
    }

    /**
     * Store a newly created vehicle control item.
     */
    public function store(VehicleControlItemStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            // Check if control belongs to user's agency
            $control = VehicleControl::findOrFail($data['vehicle_control_id']);
            $agencyId = Auth::guard('backoffice')->user()->agency_id;
            
            if ($control->agency_id !== $agencyId) {
                abort(403, 'Accès non autorisé');
            }

            $item = VehicleControlItem::create($data);
            
            // FIXED: Use correct module name 'control-item' and the actual item object
            $this->createNotification('store', 'control-item', $item);
            
            DB::commit();

            return redirect()
                ->route('backoffice.control-items.index')
                ->with('toast', [
                    'title' => 'Créé',
                    'message' => 'Élément de contrôle créé avec succès.',
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
     * Display the specified vehicle control item.
     */
    public function show(VehicleControlItem $item)
    {
        // Check if item's control belongs to user's agency
        $agencyId = Auth::guard('backoffice')->user()->agency_id;
        if ($item->vehicleControl->agency_id !== $agencyId) {
            abort(403, 'Accès non autorisé');
        }

        $item->load('vehicleControl');

        return view('Backoffice.control-items.show', compact('item'));
    }

    /**
     * Show the form for editing the specified vehicle control item.
     */
    public function edit(VehicleControlItem $item)
    {
        // Check if item's control belongs to user's agency
        $agencyId = Auth::guard('backoffice')->user()->agency_id;
        if ($item->vehicleControl->agency_id !== $agencyId) {
            abort(403, 'Accès non autorisé');
        }

        $controls = VehicleControl::where('agency_id', $agencyId)
            ->orderBy('control_number')
            ->get();

        return view('Backoffice.control-items.partials._modal_edit', compact('item', 'controls'));
    }

    /**
     * Update the specified vehicle control item.
     */
    public function update(VehicleControlItemUpdateRequest $request, VehicleControlItem $item)
    {
        // Check if item's control belongs to user's agency
        $agencyId = Auth::guard('backoffice')->user()->agency_id;
        if ($item->vehicleControl->agency_id !== $agencyId) {
            abort(403, 'Accès non autorisé');
        }

        try {
            DB::beginTransaction();

            $data = $request->validated();
            
            $item->update($data);
            
            // ADDED: Create notification for update
            $this->createNotification('update', 'control-item', $item);

            DB::commit();

            return redirect()
                ->route('backoffice.control-items.show', $item)
                ->with('toast', [
                    'title' => 'Mis à jour',
                    'message' => 'Élément de contrôle mis à jour avec succès.',
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
     * Remove the specified vehicle control item.
     */
    public function destroy(VehicleControlItem $item)
    {
        // Check if item's control belongs to user's agency
        $agencyId = Auth::guard('backoffice')->user()->agency_id;
        if ($item->vehicleControl->agency_id !== $agencyId) {
            abort(403, 'Accès non autorisé');
        }

        try {
            DB::beginTransaction();

            // Store item data for notification before delete
            $itemData = clone $item;
            $item->delete();
            
            // ADDED: Create notification for delete
            $this->createNotification('destroy', 'control-item', $itemData);
            
            DB::commit();

            return redirect()
                ->route('backoffice.control-items.index')
                ->with('toast', [
                    'title' => 'Supprimé',
                    'message' => 'Élément de contrôle supprimé avec succès.',
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
     * Get items by control ID (for API).
     */
    public function getByControl(Request $request)
    {
        $controlId = $request->get('control_id');
        
        if (!$controlId) {
            return response()->json([]);
        }

        $agencyId = Auth::guard('backoffice')->user()->agency_id;
        
        $items = VehicleControlItem::where('vehicle_control_id', $controlId)
            ->whereHas('vehicleControl', function($q) use ($agencyId) {
                $q->where('agency_id', $agencyId);
            })
            ->orderBy('item_key')
            ->get();

        return response()->json($items);
    }
}