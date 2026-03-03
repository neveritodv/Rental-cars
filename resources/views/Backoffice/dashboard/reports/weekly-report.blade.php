
@php
    $agencyId = auth()->guard('backoffice')->user()->agency_id;
    $weekStart = now()->startOfWeek();
    $weekEnd = now()->endOfWeek();
    
    $weekBookings = App\Models\Booking::where('agency_id', $agencyId)
        ->whereBetween('created_at', [$weekStart, $weekEnd])
        ->count();
    
    $weekRevenue = App\Models\Payment::where('agency_id', $agencyId)
        ->whereBetween('payment_date', [$weekStart, $weekEnd])
        ->sum('amount');
    
    $weekGrowth = App\Models\Booking::where('agency_id', $agencyId)
        ->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])
        ->count();
    
    $growthPercent = $weekGrowth > 0 ? round((($weekBookings - $weekGrowth) / $weekGrowth) * 100) : 0;
@endphp

<div class="card">
    <div class="card-header">
        <h6 class="card-title mb-0">Cette semaine</h6>
        <small class="text-muted">{{ $weekStart->format('d/m') }} - {{ $weekEnd->format('d/m') }}</small>
    </div>
    <div class="card-body">
        <div class="d-flex justify-content-between mb-2">
            <span>Réservations:</span>
            <span class="fw-bold">{{ $weekBookings }}</span>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <span>Revenus:</span>
            <span class="fw-bold">{{ number_format($weekRevenue, 0, ',', ' ') }} MAD</span>
        </div>
        <div class="d-flex justify-content-between">
            <span>Croissance:</span>
            <span class="fw-bold {{ $growthPercent >= 0 ? 'text-success' : 'text-danger' }}">
                {{ $growthPercent >= 0 ? '+' : '' }}{{ $growthPercent }}%
            </span>
        </div>
        <hr>
        <a href="{{ route('backoffice.dashboard.reports.weekly') }}" class="btn btn-sm btn-primary w-100">
            Rapport détaillé
        </a>
    </div>
</div>