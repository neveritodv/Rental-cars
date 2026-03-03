
@php
    $agencyId = auth()->guard('backoffice')->user()->agency_id;
    $today = now()->format('Y-m-d');
    
    $todayBookings = App\Models\Booking::where('agency_id', $agencyId)
        ->whereDate('created_at', $today)
        ->count();
    
    $todayRevenue = App\Models\Payment::where('agency_id', $agencyId)
        ->whereDate('payment_date', $today)
        ->sum('amount');
    
    $todayNewClients = App\Models\Client::where('agency_id', $agencyId)
        ->whereDate('created_at', $today)
        ->count();
@endphp

<div class="card">
    <div class="card-header">
        <h6 class="card-title mb-0">Résumé du jour</h6>
        <small class="text-muted">{{ now()->format('d/m/Y') }}</small>
    </div>
    <div class="card-body">
        <div class="d-flex justify-content-between mb-2">
            <span>Réservations:</span>
            <span class="fw-bold">{{ $todayBookings }}</span>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <span>Revenus:</span>
            <span class="fw-bold">{{ number_format($todayRevenue, 0, ',', ' ') }} MAD</span>
        </div>
        <div class="d-flex justify-content-between">
            <span>Nouveaux clients:</span>
            <span class="fw-bold">{{ $todayNewClients }}</span>
        </div>
        <hr>
        <a href="{{ route('backoffice.dashboard.reports.daily') }}" class="btn btn-sm btn-primary w-100">
            Voir le rapport complet
        </a>
    </div>
</div>