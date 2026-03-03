@php
    $agencyId = auth()->guard('backoffice')->user()->agency_id;
    $startDate = request('start_date', now()->startOfMonth());
    $endDate = request('end_date', now());
    
    $totalBookings = App\Models\Booking::where('agency_id', $agencyId)
        ->whereBetween('created_at', [$startDate, $endDate])
        ->count();
    
    $avgDuration = App\Models\Booking::where('agency_id', $agencyId)
        ->whereBetween('created_at', [$startDate, $endDate])
        ->avg('booked_days');
    
    $cancellationRate = App\Models\Booking::where('agency_id', $agencyId)
        ->whereBetween('created_at', [$startDate, $endDate])
        ->where('status', 'cancelled')
        ->count();
    
    $cancellationPercent = $totalBookings > 0 ? round(($cancellationRate / $totalBookings) * 100) : 0;
@endphp

<div class="table-responsive">
    <table class="table table-hover">
        <tr>
            <th>Total réservations</th>
            <td class="text-end fw-bold">{{ $totalBookings }}</td>
        </tr>
        <tr>
            <th>Durée moyenne (jours)</th>
            <td class="text-end fw-bold">{{ number_format($avgDuration ?? 0, 1) }}</td>
        </tr>
        <tr>
            <th>Taux d'annulation</th>
            <td class="text-end fw-bold">{{ $cancellationPercent }}%</td>
        </tr>
    </table>
</div>