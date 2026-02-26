<?php $page = 'finance-accounts'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<style>
    .btn-icon {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        color: #6c757d;
        background: transparent;
        border: 1px solid transparent;
        transition: all 0.2s;
        cursor: pointer;
    }
    
    .btn-icon:hover {
        background: #f8f9fa;
        border-color: #dee2e6;
        color: #0d6efd;
    }
    
    .btn-icon i {
        font-size: 18px;
    }
    
    .badge-info { background: #cce5ff; color: #004085; padding: 0.35rem 0.75rem; border-radius: 50px; font-weight: 500; }
    .badge-success { background: #d4edda; color: #155724; padding: 0.35rem 0.75rem; border-radius: 50px; font-weight: 500; }
    .badge-secondary { background: #e2e3e5; color: #383d41; padding: 0.35rem 0.75rem; border-radius: 50px; font-weight: 500; }
    
    .table-responsive, 
    .custom-datatable-filter, 
    .dataTables_wrapper {
        overflow: visible !important;
    }
    
    .dropdown-menu {
        z-index: 9999 !important;
    }
    
    .form-check {
        display: flex;
        justify-content: center;
        margin: 0;
        padding: 0;
    }
    
    .balance-badge { 
        background: #e8f5e9; 
        color: #2e7d32; 
        padding: 0.35rem 0.75rem; 
        border-radius: 50px; 
        font-weight: 500; 
        white-space: nowrap;
    }
    
    .default-badge {
        background: #ffc107;
        color: #856404;
        padding: 0.25rem 0.5rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 500;
        display: inline-block;
    }
    
    /* Filter Collapse */
    .collapse {
        display: none;
    }
    .collapse.show {
        display: block;
    }
</style>

<div class="page-wrapper">
    <div class="content me-4">
        @include('backoffice.finance.accounts.partials._breadcrumbs')

        <form method="GET" id="filterForm" action="{{ request()->url() }}">
            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
                <div class="d-flex align-items-center flex-wrap row-gap-3">
                    <!-- Sort Dropdown -->
                    <div class="dropdown me-2">
                        <a href="#" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" role="button">
                            <i class="ti ti-filter me-1"></i> Trier : 
                            @if(request('sort') == 'oldest') Plus anciens
                            @elseif(request('sort') == 'name_asc') Nom A-Z
                            @elseif(request('sort') == 'name_desc') Nom Z-A
                            @elseif(request('sort') == 'balance_asc') Solde ↑
                            @elseif(request('sort') == 'balance_desc') Solde ↓
                            @else Plus récents @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end p-2">
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'latest']) }}">Plus récents</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'oldest']) }}">Plus anciens</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'name_asc']) }}">Nom A-Z</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'name_desc']) }}">Nom Z-A</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'balance_desc']) }}">Solde (plus élevé)</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'balance_asc']) }}">Solde (moins élevé)</a></li>
                        </ul>
                    </div>
                    
                    <!-- Filters Toggle -->
                    <div>
                        <a href="#filtercollapse" class="filtercollapse coloumn d-inline-flex align-items-center" role="button">
                            <i class="ti ti-filter me-1"></i> Filtres
                        </a>
                    </div>
                </div>

                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                    <!-- Search -->
                    <div class="top-search me-2">
                        <div class="top-search-group position-relative">
                            <span class="input-icon"><i class="ti ti-search"></i></span>
                            <input type="text" name="search" id="searchInput" value="{{ request('search') }}" 
                                   class="form-control" placeholder="Rechercher un compte...">
                            @if(request('search'))
                                <button type="button" class="btn btn-link position-absolute" style="right: 5px; top: 50%; transform: translateY(-50%); padding: 0; color: #6c757d; z-index: 10;" onclick="clearSearch()">
                                    <i class="ti ti-x"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Add Button - contrôlé par permission CREATE -->
                    @can('financial-accounts.general.create')
                        <div class="mb-0">
                            <a href="{{ route('backoffice.finance.accounts.create') }}" class="btn btn-primary d-flex align-items-center">
                                <i class="ti ti-plus me-2"></i>Nouveau compte
                            </a>
                        </div>
                    @endcan
                </div>
            </div>

            <!-- Filters Panel -->
            <div class="collapse @if(request()->has('type') || request()->has('is_default')) show @endif" id="filtercollapse">
                <div class="filterbox p-3 mb-3 bg-light-100 rounded">
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Type</label>
                            <select name="type" form="filterForm" class="form-select" onchange="this.form.submit()">
                                <option value="">Tous</option>
                                <option value="bank" {{ request('type') == 'bank' ? 'selected' : '' }}>Banque</option>
                                <option value="cash" {{ request('type') == 'cash' ? 'selected' : '' }}>Caisse</option>
                                <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>Autre</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Compte par défaut</label>
                            <select name="is_default" form="filterForm" class="form-select" onchange="this.form.submit()">
                                <option value="">Tous</option>
                                <option value="1" {{ request('is_default') == '1' ? 'selected' : '' }}>Oui</option>
                                <option value="0" {{ request('is_default') == '0' ? 'selected' : '' }}>Non</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <a href="{{ route('backoffice.finance.accounts.index') }}" class="btn btn-sm btn-outline-danger w-100">
                                <i class="ti ti-x me-1"></i>Tout effacer
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Table -->
        <div class="custom-datatable-filter table-responsive">
            @include('backoffice.finance.accounts.partials._table', ['accounts' => $accounts])
        </div>

        <!-- Pagination -->
        @if(isset($accounts) && $accounts->total() > 0)
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted">
                Affichage de {{ $accounts->firstItem() }} à {{ $accounts->lastItem() }} sur {{ $accounts->total() }} comptes
            </div>
            <div>
                {{ $accounts->withQueryString()->links() }}
            </div>
        </div>
        @endif

    </div>

    <!-- Footer -->
    <div class="footer d-sm-flex align-items-center justify-content-between bg-white p-3">
        <p class="mb-0"><a href="javascript:void(0);">Privacy Policy</a><a href="javascript:void(0);" class="ms-4">Terms of Use</a></p>
        <p>&copy; 2025 Dreamsrent, Made with <span class="text-danger">❤</span> by <a href="javascript:void(0);" class="text-secondary">Dreams</a></p>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Main Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto search
    const form = document.getElementById('filterForm');
    const input = document.getElementById('searchInput');

    if (form && input) {
        let timer;
        input.addEventListener('input', function() {
            clearTimeout(timer);
            timer = setTimeout(() => form.submit(), 400);
        });
    }

    // Filter toggle
    const filterToggle = document.querySelector('.filtercollapse');
    const filterCollapse = document.getElementById('filtercollapse');

    if (filterToggle && filterCollapse) {
        filterToggle.removeAttribute('data-bs-toggle');
        
        filterToggle.addEventListener('click', function(e) {
            e.preventDefault();
            filterCollapse.classList.toggle('show');
        });
    }

    // Close filter when clicking outside
    document.addEventListener('click', function(e) {
        if (filterCollapse && filterCollapse.classList.contains('show')) {
            if (!filterCollapse.contains(e.target) && !filterToggle.contains(e.target)) {
                filterCollapse.classList.remove('show');
            }
        }
    });

    // Initialize dropdowns
    initializeAllDropdowns();
    
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.dropdown')) {
            closeAllDropdowns();
        }
    });
});

function initializeAllDropdowns() {
    document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(button => {
        button.removeEventListener('click', dropdownClickHandler);
        button.addEventListener('click', dropdownClickHandler);
    });
}

function dropdownClickHandler(e) {
    e.preventDefault();
    e.stopPropagation();
    
    const button = this;
    const dropdown = button.nextElementSibling;
    const isExpanded = button.getAttribute('aria-expanded') === 'true';
    
    closeAllDropdowns();
    
    if (dropdown && !isExpanded) {
        dropdown.classList.add('show');
        button.setAttribute('aria-expanded', 'true');
    }
}

function closeAllDropdowns() {
    document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
        menu.classList.remove('show');
        const toggle = menu.previousElementSibling;
        if (toggle && toggle.hasAttribute('data-bs-toggle="dropdown"')) {
            toggle.setAttribute('aria-expanded', 'false');
        }
    });
}

function clearSearch() {
    const input = document.getElementById('searchInput');
    if (input) {
        input.value = '';
        document.getElementById('filterForm').submit();
    }
}
</script>

@include('backoffice.finance.accounts.partials._modal_delete')
@endsection