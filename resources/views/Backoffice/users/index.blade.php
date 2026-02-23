<?php $page = 'users'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<div class="page-wrapper">
    <div class="content me-4">

        @include('backoffice.users.partials._breadcrumbs')

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
                                   href="{{ route('backoffice.users.index', array_merge(request()->all(), ['sort'=>'az'])) }}">
                                    A → Z
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item"
                                   href="{{ route('backoffice.users.index', array_merge(request()->all(), ['sort'=>'za'])) }}">
                                    Z → A
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item"
                                   href="{{ route('backoffice.users.index') }}">
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
                                   placeholder="Rechercher un utilisateur...">
                        </div>
                    </div>

                    <div class="mb-0">
                        <a href="javascript:void(0);"
                           class="btn btn-primary d-flex align-items-center"
                           data-bs-toggle="modal"
                           data-bs-target="#add_user">
                            <i class="ti ti-plus me-2"></i>Ajouter un utilisateur
                        </a>
                    </div>

                </div>
            </div>
        </form>


        <!-- FILTERS -->
        <div class="collapse" id="filtercollapse">
            <div class="filterbox mb-3 d-flex align-items-center">
                <h6 class="me-3">Filtres</h6>

                <!-- STATUS -->
                <div class="dropdown me-3">
                    <a href="#" class="dropdown-toggle btn btn-white"
                       data-bs-toggle="dropdown">
                        Statut :
                        @if(request('status')=='active') Actif
                        @elseif(request('status')=='inactive') Inactif
                        @elseif(request('status')=='blocked') Bloqué
                        @else Tous
                        @endif
                    </a>

                    <ul class="dropdown-menu p-2">
                        <li>
                            <a class="dropdown-item"
                               href="{{ route('backoffice.users.index', array_merge(request()->all(), ['status'=>'active'])) }}">
                                Actif
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item"
                               href="{{ route('backoffice.users.index', array_merge(request()->all(), ['status'=>'inactive'])) }}">
                                Inactif
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item"
                               href="{{ route('backoffice.users.index', array_merge(request()->all(), ['status'=>'blocked'])) }}">
                                Bloqué
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item"
                               href="{{ route('backoffice.users.index') }}">
                                Tous
                            </a>
                        </li>
                    </ul>
                </div>

                <a href="{{ route('backoffice.users.index') }}"
                   class="text-danger links">
                   Tout effacer
                </a>
            </div>
        </div>


        <!-- TABLE -->
        <div class="custom-datatable-filter table-responsive">
            @include('backoffice.users.partials._table', ['users' => $users])
        </div>

        <!-- PAGINATION -->
        <div class="table-footer">
            <div class="d-flex justify-content-end">
                {{ $users->withQueryString()->links() }}
            </div>
        </div>

    </div>

    <div class="footer d-sm-flex align-items-center justify-content-between bg-white p-3">
        <p class="mb-0">2024 © Rental Car. All rights reserved.</p>
        <p class="mb-0">v1.0</p>
    </div>
</div>


<!-- AUTO SEARCH -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    let searchInput = document.getElementById('searchInput');
    let timer;

    searchInput.addEventListener('keyup', function () {

        clearTimeout(timer);

        timer = setTimeout(function () {
            document.getElementById('filterForm').submit();
        }, 400);

    });

});
</script>

@include('backoffice.users.partials._modal_create')
@include('backoffice.users.partials._modal_edit')
@include('backoffice.users.partials._modal_delete')
@include('backoffice.users.partials._modals_js')

@endsection
