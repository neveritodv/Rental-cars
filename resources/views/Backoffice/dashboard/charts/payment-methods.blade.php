
@php
    $agencyId = auth()->guard('backoffice')->user()->agency_id;
    $startDate = request('start_date', now()->startOfMonth());
    $endDate = request('end_date', now());
    
    $methods = ['cash', 'card', 'bank_transfer', 'cheque'];
    $methodLabels = ['Espèces', 'Carte', 'Virement', 'Chèque'];
    $methodData = [];
    
    foreach ($methods as $method) {
        $methodData[] = App\Models\Payment::where('agency_id', $agencyId)
            ->where('method', $method)
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->sum('amount');
    }
@endphp

<div class="card flex-fill">
    <div class="card-header">
        <h5 class="card-title mb-0">Méthodes de paiement</h5>
    </div>
    <div class="card-body">
        <canvas id="paymentChart" height="150"></canvas>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('paymentChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($methodLabels) !!},
            datasets: [{
                data: {!! json_encode($methodData) !!},
                backgroundColor: [
                    chartColors.success,
                    chartColors.primary,
                    chartColors.info,
                    chartColors.warning
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.raw || 0;
                            let total = context.dataset.data.reduce((a, b) => a + b, 0);
                            let percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return `${label}: ${value.toLocaleString()} MAD (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
});
</script>