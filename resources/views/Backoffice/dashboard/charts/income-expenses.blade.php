
@php
    $agencyId = auth()->guard('backoffice')->user()->agency_id;
    $startDate = request('start_date', now()->startOfMonth());
    $endDate = request('end_date', now());
    
    $incomeData = [];
    $expenseData = [];
    $labels = [];
    
    $current = \Carbon\Carbon::parse($startDate);
    $end = \Carbon\Carbon::parse($endDate);
    
    while ($current <= $end) {
        $labels[] = $current->format('d M');
        
        // Revenus (income)
        $incomeData[] = App\Models\Payment::where('agency_id', $agencyId)
            ->whereDate('payment_date', $current)
            ->where('status', 'confirmed')
            ->sum('amount');
        
        // Dépenses (expenses) - à adapter selon votre modèle
        $expenseData[] = App\Models\FinancialTransaction::where('agency_id', $agencyId)
            ->whereDate('date', $current)
            ->where('type', 'expense')
            ->sum('amount');
        
        $current->addDay();
    }
@endphp

<div class="card flex-fill">
    <div class="card-header">
        <h5 class="card-title mb-0">Revenus vs Dépenses</h5>
    </div>
    <div class="card-body">
        <canvas id="incomeExpenseChart" height="100"></canvas>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('incomeExpenseChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($labels) !!},
            datasets: [
                {
                    label: 'Revenus',
                    data: {!! json_encode($incomeData) !!},
                    backgroundColor: chartColors.success + '80',
                    borderColor: chartColors.success,
                    borderWidth: 1
                },
                {
                    label: 'Dépenses',
                    data: {!! json_encode($expenseData) !!},
                    backgroundColor: chartColors.danger + '80',
                    borderColor: chartColors.danger,
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
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