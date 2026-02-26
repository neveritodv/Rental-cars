<head>
    <style>
        /* FIX: allow dropdowns inside tables */
        .table-responsive,
        .custom-datatable-filter {
            overflow: visible !important;
        }
    </style>
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
</head>
<?php $page = 'models'; ?>
@extends('layout.mainlayout_admin')
@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-4">

            {{-- Breadcrumb --}}
            @include('backoffice.vehicle-models.partials._breadcrumbs')

            <!-- FILTER FORM -->
            <form method="GET" id="filterForm" action="{{ route('backoffice.vehicle-models.index') }}">
                <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
                    
                    <!-- SEARCH -->
                    <div class="top-search me-2">
                        <div class="top-search-group position-relative">
                            <span class="input-icon">
                                <i class="ti ti-search"></i>
                            </span>
                            <input 
                                type="text" 
                                name="search"
                                id="searchInput"
                                class="form-control" 
                                placeholder="Rechercher un modèle ou une marque..." 
                                value="{{ request('search') }}" 
                                autocomplete="off"
                            >
                            @if(request('search'))
                                <button 
                                    type="button" 
                                    class="btn btn-link position-absolute" 
                                    style="right: 5px; top: 50%; transform: translateY(-50%); padding: 0; color: #6c757d; z-index: 10;"
                                    onclick="clearSearch()"
                                >
                                    <i class="ti ti-x"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Bouton Ajouter - contrôlé par permission CREATE -->
                    @can('vehicle-models.general.create')
                    <div class="mb-0">
                        <a href="javascript:void(0);"
                           class="btn btn-primary d-flex align-items-center"
                           data-bs-toggle="modal"
                           data-bs-target="#add_model">
                            <i class="ti ti-plus me-2"></i>Ajouter un modèle
                        </a>
                    </div>
                    @endcan
                </div>
            </form>
            <!-- /FILTER FORM -->

            <!-- Custom Data Table -->
            <div class="custom-datatable-filter table-responsive">
                @include('backoffice.vehicle-models.partials._table', ['models' => $models, 'permissions' => $permissions])
            </div>
            <!-- Custom Data Table -->

            <!-- Pagination -->
            @if($models->total() > 0)
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Affichage de {{ $models->firstItem() }} à {{ $models->lastItem() }} sur {{ $models->total() }} modèles
                </div>
                <div>
                    {{ $models->withQueryString()->links() }}
                </div>
            </div>
            @endif

        </div>

        <!-- Footer -->
        <div class="footer d-sm-flex align-items-center justify-content-between bg-white p-3">
            <p class="mb-0">
                <a href="#">Politique de confidentialité</a>
                <a href="#" class="ms-4">Conditions d’utilisation</a>
            </p>
            <p>&copy; 2025 Dreamsrent, Made with <span class="text-danger">❤</span> by
                <a href="#" class="text-secondary">Dreams</a>
            </p>
        </div>
        <!-- /Footer -->
    </div>
    <!-- /Page Wrapper -->

    {{-- Modals --}}
    @include('backoffice.vehicle-models.partials._modal_create')
    @include('backoffice.vehicle-models.partials._modal_edit')
    @include('backoffice.vehicle-models.partials._modal_delete')
    @include('backoffice.vehicle-models.partials._modals_js')
@endsection

<!-- AUTO SEARCH SCRIPT -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('filterForm');
    const searchInput = document.getElementById('searchInput');

    if (!form || !searchInput) {
        console.error('Form or search input not found');
        return;
    }

    let timer;

    searchInput.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(function () {
            form.submit();
        }, 400);
    });
});

// Clear search function
function clearSearch() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.value = '';
        document.getElementById('filterForm').submit();
    }
}
</script>