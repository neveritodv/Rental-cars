
@php
    $agencyId = auth()->guard('backoffice')->user()->agency_id;
    $startDate = request('start_date', now()->startOfMonth());
    $endDate = request('end_date', now());
    
    // Vehicle stats
    $totalVehicles = App\Models\Vehicle::where('agency_id', $agencyId)->count();
    $availableVehicles = App\Models\Vehicle::where('agency_id', $agencyId)->where('status', 'available')->count();
    
    // Booking stats
    $totalBookings = App\Models\Booking::where('agency_id', $agencyId)
        ->whereBetween('created_at', [$startDate, $endDate])
        ->count();
    $activeBookings = App\Models\Booking::where('agency_id', $agencyId)
        ->where('status', 'confirmed')
        ->whereBetween('start_date', [$startDate, $endDate])
        ->count();
    
    // Client stats
    $totalClients = App\Models\Client::where('agency_id', $agencyId)->count();
    $newClients = App\Models\Client::where('agency_id', $agencyId)
        ->whereBetween('created_at', [$startDate, $endDate])
        ->count();
    
    // Revenue stats
    $totalRevenue = App\Models\Payment::where('agency_id', $agencyId)
        ->whereBetween('payment_date', [$startDate, $endDate])
        ->sum('amount');
    $pendingPayments = App\Models\Payment::where('agency_id', $agencyId)
        ->where('status', 'pending')
        ->sum('amount');
@endphp

<div class="row g-3 mb-4">
    <div class="col-xl-3 col-sm-6">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-white-50 mb-2">Véhicules</h6>
                        <h3 class="text-white mb-1">{{ $totalVehicles }}</h3>
                        <small class="text-white-50">{{ $availableVehicles }} disponibles</small>
                    </div>
                    <i class="ti ti-car fs-45 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-white-50 mb-2">Réservations</h6>
                        <h3 class="text-white mb-1">{{ $totalBookings }}</h3>
                        <small class="text-white-50">{{ $activeBookings }} actives</small>
                    </div>
                    <i class="ti ti-calendar-stats fs-45 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-white-50 mb-2">Clients</h6>
                        <h3 class="text-white mb-1">{{ $totalClients }}</h3>
                        <small class="text-white-50">+{{ $newClients }} nouveaux</small>
                    </div>
                    <i class="ti ti-users fs-45 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-white-50 mb-2">Revenus</h6>
                        <h3 class="text-white mb-1">{{ number_format($totalRevenue, 0, ',', ' ') }} MAD</h3>
                        <small class="text-white-50">En attente: {{ number_format($pendingPayments, 0, ',', ' ') }} MAD</small>
                    </div>
                    <i class="ti ti-currency-dollar fs-45 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>