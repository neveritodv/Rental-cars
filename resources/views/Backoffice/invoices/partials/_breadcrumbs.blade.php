<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h4 class="mb-1">Factures</h4>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('backoffice.dashboard') }}">Accueil</a></li>
                <li class="breadcrumb-item active">Factures</li>
            </ol>
        </nav>
    </div>
    <div class="mt-2 mt-md-0">
        <!-- <a href="{{ route('backoffice.invoices.create') }}" class="btn btn-primary">
            <i class="ti ti-plus me-1"></i>Nouvelle facture
        </a> -->
    </div>
</div>

<!-- Statistics Cards -->
@php
    $agencyId = auth()->guard('backoffice')->user()->agency_id;
    $totalInvoices = App\Models\Invoice::where('agency_id', $agencyId)->count();
    $paidInvoices = App\Models\Invoice::where('agency_id', $agencyId)->where('status', 'paid')->count();
    $pendingInvoices = App\Models\Invoice::where('agency_id', $agencyId)->where('status', 'sent')->count();
    $totalAmount = App\Models\Invoice::where('agency_id', $agencyId)->sum('total_ttc');
@endphp

<div class="row g-3 mb-4">
    <div class="col-xl-3 col-sm-6">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-white-50 mb-2">Total Factures</h6>
                        <h3 class="text-white mb-0">{{ $totalInvoices }}</h3>
                    </div>
                    <i class="ti ti-file-invoice fs-40 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-white-50 mb-2">Payées</h6>
                        <h3 class="text-white mb-0">{{ $paidInvoices }}</h3>
                    </div>
                    <i class="ti ti-circle-check fs-40 opacity-50"></i>
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
                        <h3 class="text-white mb-0">{{ $pendingInvoices }}</h3>
                    </div>
                    <i class="ti ti-clock fs-40 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card bg-info text-white">
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
</div>