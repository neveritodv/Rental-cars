
@php
    $agencyId = auth()->guard('backoffice')->user()->agency_id;
    $startDate = request('start_date', now()->startOfMonth());
    $endDate = request('end_date', now());
    
    $occupancyData = [];
    $labels = [];
    $totalVehicles = App\Models\Vehicle::where('agency_id', $agencyId)->count();
    
    $current = \Carbon\Carbon::parse($startDate);
    $end = \Carbon\Carbon::parse($endDate);
    
    while ($current <= $end) {
        $labels[] = $current->format('d M');
        
        $rentedCount = App\Models\Booking::where('agency_id', $agencyId)
            ->where('status', 'confirmed')
            ->whereDate('start_date', '<=', $current)
            ->whereDate('end_date', '>=', $current)
            ->count();
        
        $occupancyData[] = $totalVehicles > 0 ? round(($rentedCount / $totalVehicles) * 100) : 0;
        
        $current->addDay();
    }
@endphp

<div class="card flex-fill">
    <div class="card-header">
        <h5 class="card-title mb-0">Taux d'occupation (%)</h5>
    </div>
    <div class="card-body">
        <canvas id="occupancyChart" height="100"></canvas>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('occupancyChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($labels) !!},
            datasets: [{
                label: 'Taux d\'occupation',
                data: {!! json_encode($occupancyData) !!},
                borderColor: chartColors.purple,
                backgroundColor: chartColors.purple + '20',
                borderWidth: 2,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.raw + '%';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            }
        }
    });
});
</script>