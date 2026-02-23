<?php $page = 'agencies'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<div class="page-wrapper">
    <div class="content me-4">

        @include('backoffice.agencies.partials._breadcrumbs')

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
                                   href="{{ route('backoffice.agencies.index', array_merge(request()->all(), ['sort' => 'az'])) }}">
                                    A → Z
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item"
                                   href="{{ route('backoffice.agencies.index', array_merge(request()->all(), ['sort' => 'za'])) }}">
                                    Z → A
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item"
                                   href="{{ route('backoffice.agencies.index') }}">
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
                                   placeholder="Rechercher une agence...">
                        </div>
                    </div>

                    <div class="mb-0">
                        <a href="javascript:void(0);"
                           class="btn btn-primary d-flex align-items-center"
                           data-bs-toggle="modal"
                           data-bs-target="#add_agency">
                            <i class="ti ti-plus me-2"></i>Ajouter une agence
                        </a>
                    </div>

                </div>
            </div>
        </form>

        <!-- FILTER COLLAPSE -->
        <div class="collapse {{ request()->has('status') ? 'show' : '' }}" id="filtercollapse">
            <div class="filterbox mb-3 d-flex align-items-center">
                <h6 class="me-3">Filtres</h6>

                <!-- STATUS FILTER -->
                <div class="dropdown me-3">
                    <a href="#" class="dropdown-toggle btn btn-white"
                       data-bs-toggle="dropdown">
                        Statut :
                        @if(request('status') === '1')
                            Active
                        @elseif(request('status') === '0')
                            Inactive
                        @else
                            Tous
                        @endif
                    </a>

                    <ul class="dropdown-menu p-2">
                        <li>
                            <a class="dropdown-item"
                               href="{{ route('backoffice.agencies.index', array_merge(request()->all(), ['status' => 1])) }}">
                                Active
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item"
                               href="{{ route('backoffice.agencies.index', array_merge(request()->all(), ['status' => 0])) }}">
                                Inactive
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item"
                               href="{{ route('backoffice.agencies.index') }}">
                                Tous
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- RESET -->
                <a href="{{ route('backoffice.agencies.index') }}"
                   class="text-danger links">
                   Tout effacer
                </a>
            </div>
        </div>

        <!-- TABLE -->
        <div class="custom-datatable-filter table-responsive">
            @if($agencies->count())
                @include('backoffice.agencies.partials._table', ['agencies' => $agencies])
            @else
                <div class="text-center py-5">
                    <h6 class="text-muted">Aucune agence trouvée.</h6>
                </div>
            @endif
        </div>

        <!-- PAGINATION -->
        <div class="table-footer">
            <div class="d-flex justify-content-end">
                {{ $agencies->withQueryString()->links() }}
            </div>
        </div>

    </div>

    <!-- Footer -->
    <div class="footer d-sm-flex align-items-center justify-content-between bg-white p-3">
        <p class="mb-0">
            <a href="#">Privacy Policy</a>
            <a href="#" class="ms-4">Terms of Use</a>
        </p>
        <p>&copy; 2025 Dreamsrent</p>
    </div>
</div>

<!-- AUTO SEARCH SCRIPT -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('filterForm');
    const searchInput = document.getElementById('searchInput');

    if (!form || !searchInput) return;

    let timer;

    searchInput.addEventListener('input', function () {

        clearTimeout(timer);

        timer = setTimeout(function () {
            form.submit();
        }, 400);

    });
});
</script>

@include('backoffice.agencies.partials._modal_create')
@include('backoffice.agencies.partials._modal_edit')
@include('backoffice.agencies.partials._modal_delete')
@include('backoffice.agencies.partials._modals_js')

@endsection