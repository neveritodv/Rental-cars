<head>
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
<?php $page = 'brands'; ?>
@extends('layout.mainlayout_admin')
@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-4">

            {{-- Breadcrumb --}}
            @include('backoffice.vehicle-brands.partials._breadcrumbs')

            <!-- FILTER FORM -->
            <form method="GET" id="filterForm" action="{{ route('backoffice.vehicle-brands.index') }}">
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
                                placeholder="Rechercher une marque..." 
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

                    <!-- STATUS DROPDOWN - VISUAL ONLY -->
                    <div class="dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center"
                            data-bs-toggle="dropdown">
                            <i class="ti ti-badge me-1"></i> Status
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end p-2">
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item rounded-1">Active</a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item rounded-1">Inactive</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </form>
            <!-- /FILTER FORM -->

            <!-- Custom Data Table -->
            <div class="custom-datatable-filter table-responsive">
                @include('backoffice.vehicle-brands.partials._table', ['brands' => $brands])
            </div>
            <!-- Custom Data Table -->

            <!-- Pagination -->
            <div class="table-footer">
                <div class="d-flex justify-content-end">
                    {{ $brands->withQueryString()->links() }}
                </div>
            </div>

        </div>

        <!-- Footer -->
        <div class="footer d-sm-flex align-items-center justify-content-between bg-white p-3">
            <p class="mb-0">
                <a href="#">Privacy Policy</a>
                <a href="#" class="ms-4">Terms of Use</a>
            </p>
            <p>&copy; 2025 Dreamsrent, Made with <span class="text-danger">❤</span> by 
                <a href="#" class="text-secondary">Dreams</a>
            </p>
        </div>
        <!-- /Footer -->
    </div>
    <!-- /Page Wrapper -->

    {{-- Modals --}}
    @include('backoffice.vehicle-brands.partials._modal_create')
    @include('backoffice.vehicle-brands.partials._modal_edit')
    @include('backoffice.vehicle-brands.partials._modal_delete')
    @include('backoffice.vehicle-brands.partials._modals_js')
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