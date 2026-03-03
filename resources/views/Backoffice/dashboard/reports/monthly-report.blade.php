
@php
    $agencyId = auth()->guard('backoffice')->user()->agency_id;
    $monthStart = now()->startOfMonth();
    $monthEnd = now()->endOfMonth();
    
    $monthBookings = App\Models\Booking::where('agency_id', $agencyId)
        ->whereBetween('created_at', [$monthStart, $monthEnd])
        ->count();
    
    $monthRevenue = App\Models\Payment::where('agency_id', $agencyId)
        ->whereBetween('payment_date', [$monthStart, $monthEnd])
        ->sum('amount');
    
    $lastMonthBookings = App\Models\Booking::where('agency_id', $agencyId)
        ->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
        ->count();
    
    $growthPercent = $lastMonthBookings > 0 ? round((($monthBookings - $lastMonthBookings) / $lastMonthBookings) * 100) : 0;
    
    $occupancyRate = App\Models\Vehicle::where('agency_id', $agencyId)->count() > 0 
        ? round((App\Models\Booking::where('agency_id', $agencyId)
            ->where('status', 'confirmed')
            ->whereBetween('start_date', [$monthStart, $monthEnd])
            ->count() / App\Models\Vehicle::where('agency_id', $agencyId)->count()) * 100)
        : 0;
@endphp

<div class="card">
    <div class="card-header">
        <h6 class="card-title mb-0">Ce mois</h6>
        <small class="text-muted">{{ $monthStart->format('M Y') }}</small>
    </div>
    <div class="card-body">
        <div class="d-flex justify-content-between mb-2">
            <span>Réservations:</span>
            <span class="fw-bold">{{ $monthBookings }}</span>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <span>Revenus:</span>
            <span class="fw-bold">{{ number_format($monthRevenue, 0, ',', ' ') }} MAD</span>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <span>Taux occupation:</span>
            <span class="fw-bold">{{ $occupancyRate }}%</span>
        </div>
        <div class="d-flex justify-content-between">
            <span>vs mois dernier:</span>
            <span class="fw-bold {{ $growthPercent >= 0 ? 'text-success' : 'text-danger' }}">
                {{ $growthPercent >= 0 ? '+' : '' }}{{ $growthPercent }}%
            </span>
        </div>
        <hr>
        <a href="{{ route('backoffice.dashboard.reports.monthly') }}" class="btn btn-sm btn-primary w-100">
            Rapport mensuel
        </a>
    </div>
</div>