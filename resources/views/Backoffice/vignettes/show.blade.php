<?php $page = 'vignette-details'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content me-0">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="mb-3">
                    <a href="{{ route('backoffice.vehicles.vignettes.index', $vehicle->id) }}" class="d-inline-flex align-items-center fw-medium">
                        <i class="ti ti-arrow-left me-1"></i>
                        Retour à la liste
                    </a>
                </div>

                {{-- BASIC CARD --}}
                <div class="card">
                    <div class="card-body">
                        <div class="border-bottom mb-3 pb-3">
                            <h5>Détails de la vignette</h5>
                        </div>

                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-lg me-3" style="border-radius: 10px; background-color: #f0f3f8;">
                                    <span class="avatar-title fw-bold fs-24 text-primary">
                                        <i class="ti ti-ticket"></i>
                                    </span>
                                </span>

                                <div>
                                    <h6 class="mb-1">Vignette {{ $vignette->year }}</h6>
                                    <div class="d-flex align-items-center">
                                        <p class="mb-0 me-2">
                                            <i class="ti ti-calendar me-1"></i>
                                            {{ $vignette->formatted_date }}
                                        </p>
                                        <p class="mb-0">
                                            <i class="ti ti-currency-dirham me-1"></i>
                                            {{ $vignette->formatted_amount }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center flex-wrap gap-3">
                                <span class="badge badge-md bg-primary-transparent">
                                    <i class="ti ti-calendar me-1"></i>Année: {{ $vignette->year }}
                                </span>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- TABS CARD --}}
                <div class="card mb-4 mb-xl-0">
                    <div class="card-header py-0">
                        <ul class="nav nav-tabs nav-tabs-bottom tab-dark">
                            <li class="nav-item">
                                <a class="nav-link active" href="#vignette-overview" data-bs-toggle="tab">Overview</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#vignette-notes" data-bs-toggle="tab">Notes</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#vignette-history" data-bs-toggle="tab">History</a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <div class="tab-content">

                            {{-- OVERVIEW --}}
                            <div class="tab-pane fade active show" id="vignette-overview">
                                <div class="border-bottom mb-3 pb-3">
                                    <div class="row">

                                        <div class="col-md-6 col-sm-6">
                                            <div class="mb-3">
                                                <h6 class="fs-14 fw-semibold mb-1">Véhicule</h6>
                                                <p class="fs-13">
                                                    @can('vehicles.general.view')
                                                        <a href="{{ route('backoffice.vehicles.show', $vehicle->id) }}" class="text-primary">
                                                            {{ $vehicle->registration_number }} - {{ $vehicle->registration_city ?? '' }}
                                                        </a>
                                                    @else
                                                        {{ $vehicle->registration_number }} - {{ $vehicle->registration_city ?? '' }}
                                                    @endcan
                                                </p>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-sm-6">
                                            <div class="mb-3">
                                                <h6 class="fs-14 fw-semibold mb-1">Date</h6>
                                                <p class="fs-13">{{ $vignette->formatted_date }}</p>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-sm-6">
                                            <div class="mb-3">
                                                <h6 class="fs-14 fw-semibold mb-1">Montant</h6>
                                                <p class="fs-13 fw-bold text-success">{{ $vignette->formatted_amount }}</p>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-sm-6">
                                            <div class="mb-3">
                                                <h6 class="fs-14 fw-semibold mb-1">Année</h6>
                                                <p class="fs-13">{{ $vignette->year }}</p>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-sm-6">
                                            <div class="mb-3">
                                                <h6 class="fs-14 fw-semibold mb-1">Date de création</h6>
                                                <p class="fs-13">{{ $vignette->created_at->format('d M Y, H:i') }}</p>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-sm-6">
                                            <div class="mb-3">
                                                <h6 class="fs-14 fw-semibold mb-1">Dernière modification</h6>
                                                <p class="fs-13">{{ $vignette->updated_at->format('d M Y, H:i') }}</p>
                                            </div>
                                        </div>

                                        {{-- Bouton Modifier - contrôlé par permission EDIT --}}
                                        @can('vehicle-vignettes.general.edit')
                                        <div class="col-lg-12">
                                            <a href="{{ route('backoffice.vehicles.vignettes.edit', [$vehicle->id, $vignette->id]) }}"
                                               class="btn btn-primary btn-sm d-inline-flex align-items-center">
                                                <i class="ti ti-edit me-1"></i>
                                                Modifier
                                            </a>
                                        </div>
                                        @endcan

                                    </div>
                                </div>
                            </div>
                            {{-- /OVERVIEW --}}

                            {{-- NOTES --}}
                            <div class="tab-pane fade" id="vignette-notes">
                                <div class="text-muted">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <h6>Notes internes</h6>
                                        {{-- Bouton Éditer - contrôlé par permission EDIT --}}
                                        @can('vehicle-vignettes.general.edit')
                                        <a href="{{ route('backoffice.vehicles.vignettes.edit', [$vehicle->id, $vignette->id]) }}" 
                                           class="btn btn-sm btn-primary">
                                            <i class="ti ti-edit me-1"></i>Éditer
                                        </a>
                                        @endcan
                                    </div>
                                    @if($vignette->notes)
                                        <div class="p-3 bg-light-100 rounded">
                                            {{ $vignette->notes }}
                                        </div>
                                    @else
                                        <div class="text-center py-5">
                                            <i class="ti ti-notes fs-40 text-gray-3 mb-2"></i>
                                            <p class="mb-0">Aucune note disponible</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            {{-- /NOTES --}}

                            {{-- HISTORY --}}
                            <div class="tab-pane fade" id="vignette-history">
                                <div class="activity-timeline">
                                    <div class="d-flex align-items-start mb-3">
                                        <span class="badge bg-success rounded-circle p-2 me-3 mt-1">
                                            <i class="ti ti-plus fs-12"></i>
                                        </span>
                                        <div>
                                            <p class="mb-1 fw-medium">Vignette créée</p>
                                            <small class="text-muted">{{ $vignette->created_at->format('d M Y, H:i') }}</small>
                                        </div>
                                    </div>
                                    @if($vignette->updated_at && $vignette->updated_at != $vignette->created_at)
                                        <div class="d-flex align-items-start mb-3">
                                            <span class="badge bg-info rounded-circle p-2 me-3 mt-1">
                                                <i class="ti ti-edit fs-12"></i>
                                            </span>
                                            <div>
                                                <p class="mb-1 fw-medium">Dernière modification</p>
                                                <small class="text-muted">{{ $vignette->updated_at->format('d M Y, H:i') }}</small>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            {{-- /HISTORY --}}

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Footer-->
    <div class="footer d-sm-flex align-items-center justify-content-between bg-white p-3">
        <p class="mb-0">
            <a href="javascript:void(0);">Privacy Policy</a>
            <a href="javascript:void(0);" class="ms-4">Terms of Use</a>
        </p>
        <p>&copy; 2025 Dreamsrent, Made with <span class="text-danger">❤</span> by
            <a href="javascript:void(0);" class="text-secondary">Dreams</a>
        </p>
    </div>
    <!-- /Footer-->
</div>
<!-- /Page Wrapper -->

@include('Backoffice.vignettes.partials._modal_delete')
@endsection