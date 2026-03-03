
@php
    $agencyId = auth()->guard('backoffice')->user()->agency_id;
    $startDate = request('start_date', now()->startOfMonth());
    $endDate = request('end_date', now());
    
    $revenueData = [];
    $labels = [];
    
    $current = \Carbon\Carbon::parse($startDate);
    $end = \Carbon\Carbon::parse($endDate);
    
    while ($current <= $end) {
        $labels[] = $current->format('d M');
        $revenueData[] = App\Models\Payment::where('agency_id', $agencyId)
            ->whereDate('payment_date', $current)
            ->sum('amount');
        $current->addDay();
    }
@endphp

<div class="card flex-fill">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0">Revenus journaliers</h5>
        <div class="dropdown">
            <a href="javascript:void(0);" class="btn btn-icon btn-sm" data-bs-toggle="dropdown">
                <i class="ti ti-dots-vertical"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end p-2">
                <li><a class="dropdown-item" href="{{ route('backoffice.dashboard.export.pdf') }}?type=revenue">Exporter PDF</a></li>
                <li><a class="dropdown-item" href="{{ route('backoffice.dashboard.export.excel') }}?type=revenue">Exporter Excel</a></li>
            </ul>
        </div>
    </div>
    <div class="card-body">
        <canvas id="revenueChart" height="100"></canvas>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($labels) !!},
            datasets: [{
                label: 'Revenus (MAD)',
                data: {!! json_encode($revenueData) !!},
                borderColor: chartColors.success,
                backgroundColor: chartColors.success + '20',
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: chartColors.success,
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.raw.toLocaleString() + ' MAD';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString() + ' MAD';
                        }
                    }
                }
            }
        }
    });
});
</script>