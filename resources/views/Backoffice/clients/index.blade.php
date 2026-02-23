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
<?php $page = 'clients'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<div class="page-wrapper">
    <div class="content me-4">

        @include('backoffice.clients.partials._breadcrumbs')

        <!-- ALERTS -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3">
                <i class="ti ti-check-circle me-1"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-3">
                <i class="ti ti-alert-circle me-1"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif


        <!-- FILTER FORM -->
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
                                   href="{{ route('backoffice.clients.index', array_merge(request()->all(), ['sort'=>'az'])) }}">
                                    A → Z
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item"
                                   href="{{ route('backoffice.clients.index', array_merge(request()->all(), ['sort'=>'za'])) }}">
                                    Z → A
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item"
                                   href="{{ route('backoffice.clients.index') }}">
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

                <!-- SEARCH -->
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">

                    <div class="top-search me-2">
                        <div class="top-search-group">
                            <span class="input-icon">
                                <i class="ti ti-search"></i>
                            </span>
                            <input type="text"
                                   name="search"
                                   id="searchInput"
                                   value="{{ request('search') }}"
                                   class="form-control"
                                   placeholder="Rechercher un client...">
                        </div>
                    </div>

                    <div class="mb-0">
                        <a href="{{ route('backoffice.clients.create') }}"
                           class="btn btn-primary d-flex align-items-center">
                            <i class="ti ti-plus me-2"></i>Ajouter un client
                        </a>
                    </div>

                </div>

            </div>

        </form>
        <!-- END HEADER -->


        <!-- FILTER COLLAPSE -->
        <div class="collapse" id="filtercollapse">
            <div class="filterbox mb-3 d-flex align-items-center">
                <h6 class="me-3">Filtres</h6>

                <!-- AGENCY FILTER -->
                <div class="dropdown me-3">
                    <a href="#" class="dropdown-toggle btn btn-white d-inline-flex align-items-center"
                       data-bs-toggle="dropdown">
                        Agence :
                        @if(request('agency_id'))
                            {{ $agencies->firstWhere('id', request('agency_id'))->name ?? 'Toutes' }}
                        @else
                            Toutes
                        @endif
                    </a>

                    <ul class="dropdown-menu dropdown-menu-md p-2">
                        <li>
                            <a class="dropdown-item"
                               href="{{ route('backoffice.clients.index') }}">
                                Toutes
                            </a>
                        </li>

                        @foreach($agencies as $agency)
                            <li>
                                <a class="dropdown-item"
                                   href="{{ route('backoffice.clients.index', array_merge(request()->all(), ['agency_id'=>$agency->id])) }}">
                                    {{ $agency->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <a href="{{ route('backoffice.clients.index') }}"
                   class="text-danger links">
                    Tout effacer
                </a>
            </div>
        </div>


        <!-- TABLE -->
        <div class="table-responsive">
            @include('backoffice.clients.partials._table', ['clients' => $clients])
        </div>

        <!-- PAGINATION -->
        <div class="table-footer">
            <div class="d-flex justify-content-end">
                {{ $clients->withQueryString()->links() }}
            </div>
        </div>

    </div>

    <!-- FOOTER -->
    <div class="footer d-sm-flex align-items-center justify-content-between bg-white p-3">
        <p class="mb-0">2024 © Rental Car. All rights reserved.</p>
        <p class="mb-0">v1.0</p>
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
</script>

@include('backoffice.clients.partials._modal_delete')
@include('backoffice.clients.partials._modals_js')

@endsection
