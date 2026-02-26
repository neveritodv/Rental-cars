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
<?php $page = 'agents'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<div class="page-wrapper">
    <div class="content me-4">

        @include('backoffice.agents.partials._breadcrumbs')

        <!-- FILTER + SEARCH FORM -->
        <form method="GET" id="filterForm">

            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">

                <div class="d-flex align-items-center flex-wrap row-gap-3">

                    <!-- SORT -->
                    <div class="dropdown me-2">
                        <a href="#" class="dropdown-toggle btn btn-white d-inline-flex align-items-center"
                           data-bs-toggle="dropdown">
                            <i class="ti ti-filter me-1"></i>
                            Trier :
                            @if(request('sort') == 'az')
                                A → Z
                            @elseif(request('sort') == 'za')
                                Z → A
                            @else
                                Derniers
                            @endif
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end p-2">
                            <li>
                                <a class="dropdown-item"
                                   href="{{ route('backoffice.agents.index', array_merge(request()->all(), ['sort'=>'az'])) }}">
                                    A → Z
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item"
                                   href="{{ route('backoffice.agents.index', array_merge(request()->all(), ['sort'=>'za'])) }}">
                                    Z → A
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item"
                                   href="{{ route('backoffice.agents.index') }}">
                                    Derniers
                                </a>
                            </li>
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
                                   placeholder="Rechercher un agent...">
                            @if(request('search'))
                                <button type="button" class="btn btn-link position-absolute" style="right: 5px; top: 50%; transform: translateY(-50%); padding: 0; color: #6c757d; z-index: 10;" onclick="clearSearch()">
                                    <i class="ti ti-x"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Bouton Ajouter - contrôlé par permission CREATE --}}
                    @can('agents.general.create')
                        <div class="mb-0">
                            <a href="{{ route('backoffice.agents.create') }}"
                               class="btn btn-primary d-flex align-items-center">
                                <i class="ti ti-plus me-2"></i>Ajouter un agent
                            </a>
                        </div>
                    @endcan

                </div>

            </div>

        </form>
        <!-- END HEADER -->


        <!-- FILTER COLLAPSE -->
        <div class="collapse {{ request()->has('agency_id') ? 'show' : '' }}" id="filtercollapse">
            <div class="filterbox p-3 mb-3 bg-light-100 rounded">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-medium">Agence</label>
                        <select name="agency_id" form="filterForm" class="form-select" onchange="this.form.submit()">
                            <option value="">Toutes</option>
                            @foreach($agencies as $agency)
                                <option value="{{ $agency->id }}" {{ request('agency_id') == $agency->id ? 'selected' : '' }}>
                                    {{ $agency->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mt-2 d-flex align-items-end">
                        <a href="{{ route('backoffice.agents.index') }}" class="btn btn-sm btn-outline-danger w-100">
                            <i class="ti ti-x me-1"></i>Tout effacer
                        </a>
                    </div>
                </div>
            </div>
        </div>


        <!-- TABLE -->
        <div class="custom-datatable-filter table-responsive">
            @include('backoffice.agents.partials._table', ['agents' => $agents, 'permissions' => $permissions])
        </div>

        <!-- PAGINATION -->
        @if($agents->total() > 0)
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted">
                Affichage de {{ $agents->firstItem() }} à {{ $agents->lastItem() }} sur {{ $agents->total() }} agents
            </div>
            <div>
                {{ $agents->withQueryString()->links() }}
            </div>
        </div>
        @endif

    </div>

    <div class="footer d-sm-flex align-items-center justify-content-between bg-white p-3">
        <p class="mb-0">2025 © Dreamsrent, Made with <span class="text-danger">❤</span> by <a href="#" class="text-secondary">Dreams</a></p>
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

@include('backoffice.agents.partials._modal_delete')

@endsection