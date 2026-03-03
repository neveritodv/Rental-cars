@php
    $agencyId = auth()->guard('backoffice')->user()->agency_id;
    $totalBookings = App\Models\Booking::where('agency_id', $agencyId)->count();
    $confirmedBookings = App\Models\Booking::where('agency_id', $agencyId)->where('status', 'confirmed')->count();
    $pendingBookings = App\Models\Booking::where('agency_id', $agencyId)->where('status', 'pending')->count();
    $todayBookings = App\Models\Booking::where('agency_id', $agencyId)
        ->whereDate('created_at', today())
        ->count();
@endphp

<div class="row g-3 mb-4">
    <div class="col-xl-3 col-sm-6">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-white-50 mb-2">Total Réservations</h6>
                        <h3 class="text-white mb-0">{{ $totalBookings }}</h3>
                    </div>
                    <i class="ti ti-calendar-stats fs-40 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-white-50 mb-2">Confirmées</h6>
                        <h3 class="text-white mb-0">{{ $confirmedBookings }}</h3>
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
                        <h6 class="text-white-50 mb-2">En attente</h6>
                        <h3 class="text-white mb-0">{{ $pendingBookings }}</h3>
                    </div>
                    <i class="ti ti-clock fs-40 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-white-50 mb-2">Aujourd'hui</h6>
                        <h3 class="text-white mb-0">{{ $todayBookings }}</h3>
                    </div>
                    <i class="ti ti-calendar fs-40 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>