
@php
    $agencyId = auth()->guard('backoffice')->user()->agency_id;
    
    $statuses = ['available', 'rented', 'maintenance', 'unavailable'];
    $vehicleData = [];
    $statusLabels = [
        'available' => 'Disponibles',
        'rented' => 'En location',
        'maintenance' => 'Maintenance',
        'unavailable' => 'Indisponibles'
    ];
    
    foreach ($statuses as $status) {
        $vehicleData[$status] = App\Models\Vehicle::where('agency_id', $agencyId)
            ->where('status', $status)
            ->count();
    }
@endphp

<div class="card flex-fill">
    <div class="card-header">
        <h5 class="card-title mb-0">État du parc</h5>
    </div>
    <div class="card-body">
        <canvas id="vehicleChart" height="150"></canvas>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('vehicleChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: [
                'Disponibles ({{ $vehicleData["available"] }})',
                'En location ({{ $vehicleData["rented"] }})',
                'Maintenance ({{ $vehicleData["maintenance"] }})',
                'Indisponibles ({{ $vehicleData["unavailable"] }})'
            ],
            datasets: [{
                data: [
                    {{ $vehicleData['available'] ?? 0 }},
                    {{ $vehicleData['rented'] ?? 0 }},
                    {{ $vehicleData['maintenance'] ?? 0 }},
                    {{ $vehicleData['unavailable'] ?? 0 }}
                ],
                backgroundColor: [
                    chartColors.success,
                    chartColors.primary,
                    chartColors.warning,
                    chartColors.danger
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
});
</script>