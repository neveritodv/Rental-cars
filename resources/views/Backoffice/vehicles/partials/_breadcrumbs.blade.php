@php
    $agencyId = auth()->guard('backoffice')->user()->agency_id;
    $totalVehicles = App\Models\Vehicle::where('agency_id', $agencyId)->count();
    $availableVehicles = App\Models\Vehicle::where('agency_id', $agencyId)->where('status', 'available')->count();
    $rentedVehicles = App\Models\Vehicle::where('agency_id', $agencyId)->where('status', 'rented')->count();
    $maintenanceVehicles = App\Models\Vehicle::where('agency_id', $agencyId)->where('status', 'maintenance')->count();
@endphp

<div class="row g-3 mb-4">
    <div class="col-xl-3 col-sm-6">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-white-50 mb-2">Total Véhicules</h6>
                        <h3 class="text-white mb-0">{{ $totalVehicles }}</h3>
                    </div>
                    <i class="ti ti-car fs-40 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-white-50 mb-2">Disponibles</h6>
                        <h3 class="text-white mb-0">{{ $availableVehicles }}</h3>
                    </div>
                    <i class="ti ti-circle-check fs-40 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-white-50 mb-2">En location</h6>
                        <h3 class="text-white mb-0">{{ $rentedVehicles }}</h3>
                    </div>
                    <i class="ti ti-clock fs-40 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-white-50 mb-2">Maintenance</h6>
                        <h3 class="text-white mb-0">{{ $maintenanceVehicles }}</h3>
                    </div>
                    <i class="ti ti-tool fs-40 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>