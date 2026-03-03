
@php
    $agencyId = auth()->guard('backoffice')->user()->agency_id;
    $startDate = request('start_date', now()->startOfMonth());
    $endDate = request('end_date', now());
    
    $statuses = ['pending', 'confirmed', 'completed', 'cancelled'];
    $bookingData = [];
    
    foreach ($statuses as $status) {
        $bookingData[$status] = App\Models\Booking::where('agency_id', $agencyId)
            ->where('status', $status)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
    }
@endphp

<div class="card flex-fill">
    <div class="card-header">
        <h5 class="card-title mb-0">Statut des réservations</h5>
    </div>
    <div class="card-body">
        <canvas id="bookingChart" height="150"></canvas>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('bookingChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['En attente', 'Confirmées', 'Terminées', 'Annulées'],
            datasets: [{
                data: [
                    {{ $bookingData['pending'] ?? 0 }},
                    {{ $bookingData['confirmed'] ?? 0 }},
                    {{ $bookingData['completed'] ?? 0 }},
                    {{ $bookingData['cancelled'] ?? 0 }}
                ],
                backgroundColor: [
                    chartColors.warning,
                    chartColors.success,
                    chartColors.info,
                    chartColors.danger
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            },
            cutout: '60%'
        }
    });
});
</script>