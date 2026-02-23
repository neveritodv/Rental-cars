@section('content')
<script>
// Override the native alert function to block DataTables warnings
(function() {
    // Store the original alert
    var originalAlert = window.alert;
    
    // Replace with filtered version
    window.alert = function(message) {
        // Check if this is a DataTables warning
        if (message && (message.includes('DataTables') || message.includes('datatables'))) {
            console.log('DataTables warning blocked:', message);
            return; // Block the alert
        }
        // Allow other alerts through
        originalAlert(message);
    };
    
    // Also set DataTables error mode if available
    if (window.$ && $.fn && $.fn.dataTable) {
        $.fn.dataTable.ext.errMode = 'none';
    }
})();
</script>
<?php $page = 'insurances'; ?>

@extends('layout.mainlayout_admin')

@section('content')
<div class="page-wrapper">
    <div class="content me-4">

        @include('Backoffice.insurances.partials._breadcrumbs', ['vehicle' => $vehicle ?? null])

        <!-- @if(!isset($vehicle) || !$vehicle)
            <div class="d-flex align-items-center justify-content-between p-4 mb-4 bg-light rounded border">
                <div class="d-flex align-items-center">
                    <i class="ti ti-info-circle fs-5 text-primary me-2"></i>
                    <span>Aucun véhicule trouvé. Veuillez créer un véhicule pour ajouter des assurances.</span>
                </div>
                <a href="{{ route('backoffice.vehicles.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i>Créer un véhicule
                </a>
            </div>
        @endif -->

        <form method="GET" id="filterForm" action="{{ request()->url() }}">
            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
                <div class="d-flex align-items-center flex-wrap row-gap-3">
                    <div class="dropdown me-2">
                        <a href="#" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                            <i class="ti ti-filter me-1"></i> Trier : 
                            @if(request('sort') == 'oldest') Plus anciennes
                            @elseif(request('sort') == 'amount_asc') Montant ↑
                            @elseif(request('sort') == 'amount_desc') Montant ↓
                            @elseif(request('sort') == 'next_date_asc') Échéance ↑
                            @elseif(request('sort') == 'next_date_desc') Échéance ↓
                            @else Plus récentes @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end p-2">
                            @if(isset($vehicle) && $vehicle)
                                <li><a class="dropdown-item" href="{{ route('backoffice.vehicles.insurances.index', array_merge(['vehicle' => $vehicle->id], request()->except('sort'), ['sort'=>'latest'])) }}">Plus récentes</a></li>
                                <li><a class="dropdown-item" href="{{ route('backoffice.vehicles.insurances.index', array_merge(['vehicle' => $vehicle->id], request()->except('sort'), ['sort'=>'oldest'])) }}">Plus anciennes</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('backoffice.vehicles.insurances.index', array_merge(['vehicle' => $vehicle->id], request()->except('sort'), ['sort'=>'amount_desc'])) }}">Montant (plus élevé)</a></li>
                                <li><a class="dropdown-item" href="{{ route('backoffice.vehicles.insurances.index', array_merge(['vehicle' => $vehicle->id], request()->except('sort'), ['sort'=>'amount_asc'])) }}">Montant (moins élevé)</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('backoffice.vehicles.insurances.index', array_merge(['vehicle' => $vehicle->id], request()->except('sort'), ['sort'=>'next_date_asc'])) }}">Échéance (proche)</a></li>
                                <li><a class="dropdown-item" href="{{ route('backoffice.vehicles.insurances.index', array_merge(['vehicle' => $vehicle->id], request()->except('sort'), ['sort'=>'next_date_desc'])) }}">Échéance (éloignée)</a></li>
                            @else
                                <li><a class="dropdown-item" href="{{ route('backoffice.vehicles.insurances.index', ['vehicle' => 1]) }}">Plus récentes</a></li>
                                <li><a class="dropdown-item" href="{{ route('backoffice.vehicles.insurances.index', ['vehicle' => 1, 'sort' => 'oldest']) }}">Plus anciennes</a></li>
                            @endif
                        </ul>
                    </div>
                    <div>
                        <a href="#filtercollapse" class="filtercollapse coloumn d-inline-flex align-items-center" data-bs-toggle="collapse">
                            <i class="ti ti-filter me-1"></i> Filtres
                        </a>
                    </div>
                </div>

                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                    <div class="top-search me-2">
                        <div class="top-search-group position-relative">
                            <span class="input-icon"><i class="ti ti-search"></i></span>
                            <input type="text" name="search" id="searchInput" value="{{ request('search') }}" class="form-control" placeholder="Rechercher une assurance...">
                            @if(request('search'))
                                <button type="button" class="btn btn-link position-absolute" style="right: 5px; top: 50%; transform: translateY(-50%); padding: 0; color: #6c757d; z-index: 10;" onclick="clearSearch()">
                                    <i class="ti ti-x"></i>
                                </button>
                            @endif
                        </div>
                    </div>
<div class="mb-0">
    @if(isset($isGlobalView) && $isGlobalView)
        <a href="{{ route('backoffice.vehicle-documents.insurances.create') }}" class="btn btn-primary d-flex align-items-center">
            <i class="ti ti-plus me-2"></i>Ajouter une assurance
        </a>
    @else
        <a href="{{ route('backoffice.vehicles.insurances.create', ['vehicle' => $vehicle->id]) }}" class="btn btn-primary d-flex align-items-center">
            <i class="ti ti-plus me-2"></i>Ajouter une assurance
        </a>
    @endif
</div>
                </div>
            </div>

            <div class="collapse" id="filtercollapse">
                <div class="filterbox p-3 mb-3 bg-light-100 rounded">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Compagnie</label>
                            <select name="company" form="filterForm" class="form-select" onchange="this.form.submit()">
                                <option value="">Toutes</option>
                                @foreach($availableCompanies ?? [] as $company)
                                    <option value="{{ $company }}" {{ request('company') == $company ? 'selected' : '' }}>{{ $company }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Date début</label>
                            <input type="date" form="filterForm" name="date_from" value="{{ request('date_from') }}" class="form-control" onchange="this.form.submit()">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Date fin</label>
                            <input type="date" form="filterForm" name="date_to" value="{{ request('date_to') }}" class="form-control" onchange="this.form.submit()">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Échéance du</label>
                            <input type="date" form="filterForm" name="next_date_from" value="{{ request('next_date_from') }}" class="form-control" onchange="this.form.submit()">
                        </div>
                        <div class="col-md-3 mt-2">
                            <label class="form-label fw-medium">Échéance au</label>
                            <input type="date" form="filterForm" name="next_date_to" value="{{ request('next_date_to') }}" class="form-control" onchange="this.form.submit()">
                        </div>
                        <div class="col-md-3 mt-2">
                            <label class="form-label fw-medium">Montant min (DH)</label>
                            <input type="number" form="filterForm" name="amount_min" value="{{ request('amount_min') }}" class="form-control" placeholder="0.00" step="0.01" onchange="this.form.submit()">
                        </div>
                        <div class="col-md-3 mt-2">
                            <label class="form-label fw-medium">Montant max (DH)</label>
                            <input type="number" form="filterForm" name="amount_max" value="{{ request('amount_max') }}" class="form-control" placeholder="9999.99" step="0.01" onchange="this.form.submit()">
                        </div>
                        <div class="col-md-3 mt-2 d-flex align-items-end">
                            <a href="{{ route('backoffice.vehicles.insurances.index', ['vehicle' => $vehicle->id ?? 1]) }}" class="btn btn-sm btn-outline-danger w-100">
                                <i class="ti ti-x me-1"></i>Tout effacer
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="custom-datatable-filter table-responsive">
            @include('Backoffice.insurances.partials._table')
        </div>

        @if(isset($insurances) && $insurances->total() > 0)
        <div class="table-footer">
            <div class="d-flex justify-content-end">{{ $insurances->withQueryString()->links() }}</div>
        </div>
        @endif

    </div>

    <div class="footer d-sm-flex align-items-center justify-content-between bg-white p-3">
        <p class="mb-0"><a href="javascript:void(0);">Privacy Policy</a><a href="javascript:void(0);" class="ms-4">Terms of Use</a></p>
        <p>&copy; 2025 Dreamsrent, Made with <span class="text-danger">❤</span> by <a href="javascript:void(0);" class="text-secondary">Dreams</a></p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('filterForm');
    const input = document.getElementById('searchInput');
    if (!form || !input) return;
    let debounceTimer;
    input.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function () { form.submit(); }, 400);
    });
});

function clearSearch() {
    const input = document.getElementById('searchInput');
    if (input) { input.value = ''; document.getElementById('filterForm').submit(); }
}
</script>
@include('Backoffice.insurances.partials._modals_js')
@include('Backoffice.insurances.partials._modal_delete')
@endsection