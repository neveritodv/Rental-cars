<?php $page = 'agency-subscriptions'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<div class="page-wrapper">
    <div class="content me-4">

        @include('Backoffice.agency-subscriptions.partials._breadcrumbs')

        <!-- FILTER FORM -->
        <form method="GET" id="filterForm">

            <!-- Table Header -->
            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">

                <div class="d-flex align-items-center flex-wrap row-gap-3">

                    <!-- SORT -->
                    <div class="dropdown me-2">
                        <a href="#" class="dropdown-toggle btn btn-white d-inline-flex align-items-center"
                           data-bs-toggle="dropdown">
                            <i class="ti ti-filter me-1"></i>
                            Trier :
                            @if(request('sort') == 'az')
                                Plan A → Z
                            @elseif(request('sort') == 'za')
                                Plan Z → A
                            @else
                                Derniers
                            @endif
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end p-2">
                            <li>
                                <a class="dropdown-item rounded-1"
                                   href="{{ route('backoffice.agency-subscriptions.index', array_merge(request()->all(), ['sort'=>'az'])) }}">
                                    Plan A → Z
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item rounded-1"
                                   href="{{ route('backoffice.agency-subscriptions.index', array_merge(request()->all(), ['sort'=>'za'])) }}">
                                    Plan Z → A
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item rounded-1"
                                   href="{{ route('backoffice.agency-subscriptions.index') }}">
                                    Derniers
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- FILTER TOGGLE -->
                    <div class="dropdown">
                        <a href="#filtercollapse"
                           class="filtercollapse coloumn d-inline-flex align-items-center"
                           data-bs-toggle="collapse">
                            <i class="ti ti-filter me-1"></i> Filtres
                            <span class="badge badge-xs rounded-pill bg-danger ms-2">
                                {{ collect(request()->only(['status','provider','search','sort']))->filter()->count() }}
                            </span>
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
                                   placeholder="Rechercher">
                        </div>
                    </div>

                    <div class="mb-0">
                        <a href="javascript:void(0);"
                           class="btn btn-primary d-flex align-items-center"
                           data-bs-toggle="modal"
                           data-bs-target="#create_subscription">
                            <i class="ti ti-plus me-2"></i>Ajouter un abonnement
                        </a>
                    </div>
                </div>

            </div>
        </form>
        <!-- /Table Header -->


        <!-- FILTER COLLAPSE -->
        <div class="collapse" id="filtercollapse">
            <div class="filterbox mb-3 d-flex align-items-center">
                <h6 class="me-3">Filtres</h6>

                <!-- STATUS -->
                <div class="dropdown me-3">
                    <a href="#" class="dropdown-toggle btn btn-white d-inline-flex align-items-center"
                       data-bs-toggle="dropdown">
                        Statut :
                        @if(request('status')=='active') Actif
                        @elseif(request('status')=='inactive') Inactif
                        @elseif(request('status')=='expired') Expiré
                        @elseif(request('status')=='trial') Essai
                        @else Tous
                        @endif
                    </a>

                    <ul class="dropdown-menu dropdown-menu-md p-2">
                        <li>
                            <a class="dropdown-item"
                               href="{{ route('backoffice.agency-subscriptions.index', array_merge(request()->all(), ['status'=>'active'])) }}">
                                Actif
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item"
                               href="{{ route('backoffice.agency-subscriptions.index', array_merge(request()->all(), ['status'=>'inactive'])) }}">
                                Inactif
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item"
                               href="{{ route('backoffice.agency-subscriptions.index', array_merge(request()->all(), ['status'=>'expired'])) }}">
                                Expiré
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item"
                               href="{{ route('backoffice.agency-subscriptions.index', array_merge(request()->all(), ['status'=>'trial'])) }}">
                                Essai
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- PROVIDER -->
                <div class="dropdown me-3">
                    <a href="#" class="dropdown-toggle btn btn-white d-inline-flex align-items-center"
                       data-bs-toggle="dropdown">
                        Provider :
                        {{ request('provider') ?? 'Tous' }}
                    </a>

                    <ul class="dropdown-menu dropdown-menu-md p-2">
                        <li>
                            <a class="dropdown-item"
                               href="{{ route('backoffice.agency-subscriptions.index', array_merge(request()->all(), ['provider'=>'manual'])) }}">
                                manual
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item"
                               href="{{ route('backoffice.agency-subscriptions.index', array_merge(request()->all(), ['provider'=>'stripe'])) }}">
                                stripe
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item"
                               href="{{ route('backoffice.agency-subscriptions.index', array_merge(request()->all(), ['provider'=>'paypal'])) }}">
                                paypal
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item"
                               href="{{ route('backoffice.agency-subscriptions.index', array_merge(request()->all(), ['provider'=>'other'])) }}">
                                other
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- RESET -->
                <a href="{{ route('backoffice.agency-subscriptions.index') }}"
                   class="text-danger links">
                    Tout effacer
                </a>

            </div>
        </div>


        <!-- TABLE -->
        <div class="custom-datatable-filter table-responsive">
            @include('Backoffice.agency-subscriptions.partials._table', ['subscriptions'=>$subscriptions])
        </div>

        <!-- PAGINATION -->
        <div class="table-footer">
            <div class="d-flex justify-content-end">
                {{ $subscriptions->withQueryString()->links() }}
            </div>
        </div>

    </div>

    <div class="footer d-sm-flex align-items-center justify-content-between bg-white p-3">
        <p class="mb-0">
            <a href="#">Privacy Policy</a>
            <a href="#" class="ms-4">Terms of Use</a>
        </p>
        <p>&copy; 2025 Dreamsrent</p>
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

<script>
document.addEventListener('DOMContentLoaded', function () {

    const deleteModal = document.getElementById('delete_subscription');

    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {

            const button = event.relatedTarget;

            const action = button.getAttribute('data-delete-action');
            const plan   = button.getAttribute('data-subscription-name');

            // Set form action dynamically
            const form = document.getElementById('deleteSubscriptionForm');
            form.setAttribute('action', action);

            // Optional: show plan name in modal
            document.getElementById('deletePlanName').textContent = plan;
        });
    }

});
</script>


@include('Backoffice.agency-subscriptions.partials._modal_create')
@include('Backoffice.agency-subscriptions.partials._modal_edit')
@include('Backoffice.agency-subscriptions.partials._modal_delete')
@include('Backoffice.agency-subscriptions.partials._modals_js')

@endsection
