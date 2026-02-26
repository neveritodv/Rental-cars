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
<?php $page = 'technical-checks'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<div class="page-wrapper">
    <div class="content me-4">

        @include('Backoffice.technical-checks.partials._breadcrumbs', ['vehicle' => $vehicle ?? null])

        @if(!isset($vehicle) || !$vehicle)
            <div class="text-center py-4">
                <!-- <a href="{{ route('backoffice.vehicles.create') }}" class="btn btn-primary">
                     <i class="ti ti-plus me-2"></i>Créer un véhicule 
                </a> -->
            </div>
        @endif

        <!-- FILTER + SEARCH FORM -->
        <form method="GET" id="filterForm" action="{{ request()->url() }}">

            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">

                <div class="d-flex align-items-center flex-wrap row-gap-3">

                    <!-- SORT -->
                    <div class="dropdown me-2">
                        <a href="#" class="dropdown-toggle btn btn-white d-inline-flex align-items-center"
                           data-bs-toggle="dropdown">
                            <i class="ti ti-filter me-1"></i>
                            Trier :
                            @if(request('sort') == 'oldest')
                                Plus anciennes
                            @elseif(request('sort') == 'amount_asc')
                                Montant ↑
                            @elseif(request('sort') == 'amount_desc')
                                Montant ↓
                            @elseif(request('sort') == 'next_date_asc')
                                Échéance ↑
                            @elseif(request('sort') == 'next_date_desc')
                                Échéance ↓
                            @else
                                Plus récentes
                            @endif
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end p-2">
                            @if(isset($vehicle) && $vehicle)
                                <li>
                                    <a class="dropdown-item"
                                       href="{{ route('backoffice.vehicles.technical-checks.index', array_merge(['vehicle' => $vehicle->id], request()->except('sort'), ['sort'=>'latest'])) }}">
                                        Plus récentes
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                       href="{{ route('backoffice.vehicles.technical-checks.index', array_merge(['vehicle' => $vehicle->id], request()->except('sort'), ['sort'=>'oldest'])) }}">
                                        Plus anciennes
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item"
                                       href="{{ route('backoffice.vehicles.technical-checks.index', array_merge(['vehicle' => $vehicle->id], request()->except('sort'), ['sort'=>'amount_desc'])) }}">
                                        Montant (plus élevé)
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                       href="{{ route('backoffice.vehicles.technical-checks.index', array_merge(['vehicle' => $vehicle->id], request()->except('sort'), ['sort'=>'amount_asc'])) }}">
                                        Montant (moins élevé)
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item"
                                       href="{{ route('backoffice.vehicles.technical-checks.index', array_merge(['vehicle' => $vehicle->id], request()->except('sort'), ['sort'=>'next_date_asc'])) }}">
                                        Échéance (proche)
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                       href="{{ route('backoffice.vehicles.technical-checks.index', array_merge(['vehicle' => $vehicle->id], request()->except('sort'), ['sort'=>'next_date_desc'])) }}">
                                        Échéance (éloignée)
                                    </a>
                                </li>
                            @else
                                <li>
                                    <a class="dropdown-item" href="{{ route('backoffice.vehicles.technical-checks.index', ['vehicle' => 1]) }}">
                                        Plus récentes
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('backoffice.vehicles.technical-checks.index', ['vehicle' => 1, 'sort' => 'oldest']) }}">
                                        Plus anciennes
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>

                    <!-- FILTER TOGGLE -->
                    <div>
                        <a href="#filtercollapse"
                           class="filtercollapse coloumn d-inline-flex align-items-center"
                           data-bs-toggle="collapse">
                            <i class="ti ti-filter me-1"></i> Filtres
                        </a>
                    </div>

                </div>

                <!-- SEARCH & ACTIONS -->
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">

                    <div class="top-search me-2">
                        <div class="top-search-group position-relative">
                            <span class="input-icon">
                                <i class="ti ti-search"></i>
                            </span>
                            <input type="text"
                                   name="search"
                                   id="searchInput"
                                   value="{{ request('search') }}"
                                   class="form-control"
                                   placeholder="Rechercher un contrôle technique...">
                            @if(request('search'))
                                <button type="button" class="btn btn-link position-absolute" style="right: 5px; top: 50%; transform: translateY(-50%); padding: 0; color: #6c757d; z-index: 10;" onclick="clearSearch()">
                                    <i class="ti ti-x"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Bouton Ajouter - contrôlé par permission CREATE --}}
                    @can('vehicle-technical-checks.general.create')
                        <div class="mb-0">
                            @if(isset($isGlobalView) && $isGlobalView)
                                <a href="{{ route('backoffice.vehicle-documents.technical-checks.create') }}" class="btn btn-primary d-flex align-items-center">
                                    <i class="ti ti-plus me-2"></i>Ajouter un contrôle technique
                                </a>
                            @else
                                <a href="{{ route('backoffice.vehicles.technical-checks.create', ['vehicle' => $vehicle->id]) }}" class="btn btn-primary d-flex align-items-center">
                                    <i class="ti ti-plus me-2"></i>Ajouter un contrôle technique
                                </a>
                            @endif
                        </div>
                    @endcan

                </div>

            </div>

            <!-- FILTER COLLAPSE -->
            <div class="collapse @if(request()->has('date_from') || request()->has('date_to') || request()->has('next_date_from') || request()->has('next_date_to') || request()->has('amount_min') || request()->has('amount_max')) show @endif" id="filtercollapse">
                <div class="filterbox p-3 mb-3 bg-light-100 rounded">
                    <div class="row align-items-end">
                        <div class="col-md-2">
                            <label class="form-label fw-medium">Date du</label>
                            <input type="date" 
                                   form="filterForm"
                                   name="date_from" 
                                   value="{{ request('date_from') }}"
                                   class="form-control"
                                   onchange="this.form.submit()">
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label fw-medium">Date au</label>
                            <input type="date" 
                                   form="filterForm"
                                   name="date_to" 
                                   value="{{ request('date_to') }}"
                                   class="form-control"
                                   onchange="this.form.submit()">
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label fw-medium">Échéance du</label>
                            <input type="date" 
                                   form="filterForm"
                                   name="next_date_from" 
                                   value="{{ request('next_date_from') }}"
                                   class="form-control"
                                   onchange="this.form.submit()">
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label fw-medium">Échéance au</label>
                            <input type="date" 
                                   form="filterForm"
                                   name="next_date_to" 
                                   value="{{ request('next_date_to') }}"
                                   class="form-control"
                                   onchange="this.form.submit()">
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label fw-medium">Montant min (DH)</label>
                            <input type="number" 
                                   form="filterForm"
                                   name="amount_min" 
                                   value="{{ request('amount_min') }}"
                                   class="form-control"
                                   placeholder="0.00"
                                   step="0.01"
                                   onchange="this.form.submit()">
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label fw-medium">Montant max (DH)</label>
                            <input type="number" 
                                   form="filterForm"
                                   name="amount_max" 
                                   value="{{ request('amount_max') }}"
                                   class="form-control"
                                   placeholder="9999.99"
                                   step="0.01"
                                   onchange="this.form.submit()">
                        </div>
                        
                        <div class="col-md-2 d-flex align-items-end">
                            <a href="{{ route('backoffice.vehicles.technical-checks.index', ['vehicle' => $vehicle->id ?? 1]) }}"
                               class="btn btn-sm btn-outline-danger w-100">
                                <i class="ti ti-x me-1"></i>Tout effacer
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </form>
        <!-- END HEADER -->

        <!-- TABLE -->
        <div class="custom-datatable-filter table-responsive">
            @include('Backoffice.technical-checks.partials._table', [
                'technicalChecks' => $technicalChecks,
                'isGlobalView' => $isGlobalView ?? false,
                'vehicle' => $vehicle ?? null,
                'permissions' => $permissions ?? []
            ])
        </div>

        <!-- PAGINATION -->
        @if(isset($technicalChecks) && $technicalChecks->total() > 0)
        <div class="table-footer">
            <div class="d-flex justify-content-end">
                {{ $technicalChecks->withQueryString()->links() }}
            </div>
        </div>
        @endif

    </div>

    <div class="footer d-sm-flex align-items-center justify-content-between bg-white p-3">
        <p class="mb-0">
            <a href="javascript:void(0);">Privacy Policy</a>
            <a href="javascript:void(0);" class="ms-4">Terms of Use</a>
        </p>
        <p>&copy; 2025 Dreamsrent, Made with <span class="text-danger">❤</span> by
            <a href="javascript:void(0);" class="text-secondary">Dreams</a>
        </p>
    </div>
</div>

<!-- AUTO SEARCH SCRIPT -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('filterForm');
    const input = document.getElementById('searchInput');

    if (!form || !input) return;

    let debounceTimer;

    input.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function () {
            form.submit();
        }, 400);
    });
});

function clearSearch() {
    const input = document.getElementById('searchInput');
    if (input) {
        input.value = '';
        document.getElementById('filterForm').submit();
    }
}
</script>

@include('Backoffice.technical-checks.partials._modal_delete')
@include('Backoffice.technical-checks.partials._modals_js')
@endsection