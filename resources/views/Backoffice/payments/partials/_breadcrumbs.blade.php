@php
    $agencyId = auth()->guard('backoffice')->user()->agency_id;
    $totalPayments = App\Models\Payment::where('agency_id', $agencyId)->count();
    $totalAmount = App\Models\Payment::where('agency_id', $agencyId)->sum('amount');
    $todayPayments = App\Models\Payment::where('agency_id', $agencyId)
        ->whereDate('payment_date', today())
        ->sum('amount');
    $pendingCount = App\Models\Payment::where('agency_id', $agencyId)
        ->where('status', 'pending')
        ->count();
@endphp

<div class="row g-3 mb-4">
    <div class="col-xl-3 col-sm-6">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-white-50 mb-2">Total Paiements</h6>
                        <h3 class="text-white mb-0">{{ $totalPayments }}</h3>
                    </div>
                    <i class="ti ti-credit-card fs-40 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-white-50 mb-2">Montant Total</h6>
                        <h3 class="text-white mb-0">{{ number_format($totalAmount, 2, ',', ' ') }} MAD</h3>
                    </div>
                    <i class="ti ti-currency-dollar fs-40 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-white-50 mb-2">Aujourd'hui</h6>
                        <h3 class="text-white mb-0">{{ number_format($todayPayments, 2, ',', ' ') }} MAD</h3>
                    </div>
                    <i class="ti ti-calendar fs-40 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-white-50 mb-2">En attente</h6>
                        <h3 class="text-white mb-0">{{ $pendingCount }}</h3>
                    </div>
                    <i class="ti ti-clock fs-40 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>