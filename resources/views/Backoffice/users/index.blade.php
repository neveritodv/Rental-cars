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
                                   placeholder="Rechercher un utilisateur...">
                            @if(request('search'))
                                <button type="button" class="btn btn-link position-absolute" style="right: 5px; top: 50%; transform: translateY(-50%); padding: 0; color: #6c757d; z-index: 10;" onclick="clearSearch()">
                                    <i class="ti ti-x"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Bouton Ajouter - contrôlé par permission CREATE --}}
                    @can('users.general.create')
                        <div class="mb-0">
                            <a href="javascript:void(0);"
                               class="btn btn-primary d-flex align-items-center"
                               data-bs-toggle="modal"
                               data-bs-target="#add_user">
                                <i class="ti ti-plus me-2"></i>Ajouter un utilisateur
                            </a>
                        </div>
                    @endcan

                </div>
            </div>
        </form>


        <!-- FILTERS -->
        <div class="collapse {{ request()->has('status') ? 'show' : '' }}" id="filtercollapse">
            <div class="filterbox p-3 mb-3 bg-light-100 rounded">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-medium">Statut</label>
                        <select name="status" form="filterForm" class="form-select" onchange="this.form.submit()">
                            <option value="">Tous</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                            <option value="blocked" {{ request('status') == 'blocked' ? 'selected' : '' }}>Bloqué</option>
                        </select>
                    </div>
                    <div class="col-md-3 mt-2 d-flex align-items-end">
                        <a href="{{ route('backoffice.users.index') }}" class="btn btn-sm btn-outline-danger w-100">
                            <i class="ti ti-x me-1"></i>Tout effacer
                        </a>
                    </div>
                </div>
            </div>
        </div>


        <!-- TABLE -->
        <div class="custom-datatable-filter table-responsive">
            @include('backoffice.users.partials._table', ['users' => $users, 'permissions' => $permissions])
        </div>

        <!-- PAGINATION -->
        @if($users->total() > 0)
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted">
                Affichage de {{ $users->firstItem() }} à {{ $users->lastItem() }} sur {{ $users->total() }} utilisateurs
            </div>
            <div>
                {{ $users->withQueryString()->links() }}
            </div>
        </div>
        @endif

    </div>

    <div class="footer d-sm-flex align-items-center justify-content-between bg-white p-3">
        <p class="mb-0">2025 © Dreamsrent, Made with <span class="text-danger">❤</span> by <a href="#" class="text-secondary">Dreams</a></p>
    </div>
</div>


<!-- AUTO SEARCH -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const form = document.getElementById('filterForm');

    if (searchInput && form) {
        let timer;
        searchInput.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(function () {
                form.submit();
            }, 400);
        });
    }
});

function clearSearch() {
    const input = document.getElementById('searchInput');
    if (input) {
        input.value = '';
        document.getElementById('filterForm').submit();
    }
}
</script>

@include('backoffice.users.partials._modal_create')
@include('backoffice.users.partials._modal_edit')
@include('backoffice.users.partials._modal_delete')
@include('backoffice.users.partials._modals_js')

@endsection