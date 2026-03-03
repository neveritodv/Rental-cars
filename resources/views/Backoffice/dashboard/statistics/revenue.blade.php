
@php
    $agencyId = auth()->guard('backoffice')->user()->agency_id;
    $startDate = request('start_date', now()->startOfMonth());
    $endDate = request('end_date', now());
    
    $totalRevenue = App\Models\Payment::where('agency_id', $agencyId)
        ->whereBetween('payment_date', [$startDate, $endDate])
        ->sum('amount');
    
    $avgRevenue = App\Models\Payment::where('agency_id', $agencyId)
        ->whereBetween('payment_date', [$startDate, $endDate])
        ->avg('amount');
    
    $dailyAvg = App\Models\Payment::where('agency_id', $agencyId)
        ->whereBetween('payment_date', [$startDate, $endDate])
        ->selectRaw('DATE(payment_date) as date, SUM(amount) as total')
        ->groupBy('date')
        ->get()
        ->avg('total');
@endphp

<div class="table-responsive">
    <table class="table table-hover">
        <tr>
            <th>Revenu total</th>
            <td class="text-end fw-bold">{{ number_format($totalRevenue, 2, ',', ' ') }} MAD</td>
        </tr>
        <tr>
            <th>Paiement moyen</th>
            <td class="text-end fw-bold">{{ number_format($avgRevenue ?? 0, 2, ',', ' ') }} MAD</td>
        </tr>
        <tr>
            <th>Moyenne journalière</th>
            <td class="text-end fw-bold">{{ number_format($dailyAvg ?? 0, 2, ',', ' ') }} MAD</td>
        </tr>
    </table>
</div>