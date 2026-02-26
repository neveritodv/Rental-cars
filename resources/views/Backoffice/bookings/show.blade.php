<?php $page = 'booking-details'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<style>
    .info-label {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }
    .info-value {
        font-weight: 500;
        margin-bottom: 1rem;
    }
    .badge-pending { background: #fff3cd; color: #856404; }
    .badge-confirmed { background: #d4edda; color: #155724; }
    .badge-cancelled { background: #f8d7da; color: #721c24; }
    .badge-converted { background: #cce5ff; color: #004085; }
    .amount-display {
        font-size: 1.5rem;
        font-weight: 600;
        color: #198754;
    }
    .wizard-nav {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 2rem;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 10px;
    }
    .wizard-nav .nav-item {
        flex: 1;
        min-width: 150px;
    }
    .wizard-nav .nav-link {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        background: white;
        border-radius: 8px;
        color: #6c757d;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s;
        border: 1px solid #dee2e6;
        cursor: pointer;
    }
    .wizard-nav .nav-link i {
        margin-right: 8px;
        font-size: 1.2rem;
    }
    .wizard-nav .nav-link.active {
        background: #0d6efd;
        color: white;
        border-color: #0d6efd;
    }
    .info-panel {
        display: none;
    }
    .info-panel.active {
        display: block;
    }
</style>

<div class="page-wrapper">
    <div class="content me-0">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <a href="{{ route('backoffice.bookings.index') }}" class="d-inline-flex align-items-center fw-medium">
                        <i class="ti ti-arrow-left me-1"></i> Retour à la liste
                    </a>
                    <div>
                        {{-- Bouton Modifier - contrôlé par permission EDIT --}}
                        @if(isset($permissions['can_edit']) && $permissions['can_edit'])
                        <a href="{{ route('backoffice.bookings.edit', $booking) }}" class="btn btn-primary">
                            <i class="ti ti-edit me-1"></i>Modifier
                        </a>
                        @endif
                    </div>
                </div>

                <!-- Header Card -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-lg me-3" style="border-radius: 10px; background-color: #f0f3f8;">
                                    <span class="avatar-title fw-bold fs-24 text-primary">
                                        <i class="ti ti-calendar-stats"></i>
                                    </span>
                                </span>
                                <div>
                                    <h4 class="mb-1">Réservation #{{ $booking->id }}</h4>
                                    <p class="mb-0 text-muted">
                                        <i class="ti ti-calendar me-1"></i>
                                        Créée le {{ $booking->created_at->format('d/m/Y à H:i') }}
                                    </p>
                                </div>
                            </div>
                            <div>
                                <span class="badge {{ $booking->status_badge_class }} fs-6 p-2">
                                    {{ $booking->status_text }}
                                </span>
                                <br>
                                <small class="text-muted">
                                    <i class="{{ $booking->source_icon }} me-1"></i>
                                    {{ $booking->source_text }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info Tabs -->
                <div class="wizard-nav">
                    <div class="nav-item">
                        <a class="nav-link active" data-panel="1">
                            <i class="ti ti-user"></i>
                            Client & Véhicule
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link" data-panel="2">
                            <i class="ti ti-calendar"></i>
                            Dates & Lieux
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link" data-panel="3">
                            <i class="ti ti-currency-dollar"></i>
                            Informations
                        </a>
                    </div>
                </div>

                <!-- Panel 1: Client & Véhicule -->
                <div class="info-panel active" id="panel1">
                    <div class="row">
                        <!-- Client Information -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="ti ti-user me-2"></i>
                                        Client
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($booking->client)
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="info-label">Nom</div>
                                                <div class="info-value">
                                                    {{-- Lien vers show client - contrôlé par permission VIEW sur clients --}}
                                                    @can('clients.general.view')
                                                        <a href="{{ route('backoffice.clients.show', $booking->client_id) }}">
                                                            {{ $booking->client->first_name }} {{ $booking->client->last_name }}
                                                        </a>
                                                    @else
                                                        {{ $booking->client->first_name }} {{ $booking->client->last_name }}
                                                    @endcan
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-label">Téléphone</div>
                                                <div class="info-value">{{ $booking->client->phone }}</div>
                                            </div>
                                            @if($booking->client->email)
                                            <div class="col-md-12">
                                                <div class="info-label">Email</div>
                                                <div class="info-value">{{ $booking->client->email }}</div>
                                            </div>
                                            @endif
                                        </div>
                                    @else
                                        <p class="text-muted mb-0">Aucun client associé</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Vehicle Information -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="ti ti-car me-2"></i>
                                        Véhicule
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($booking->vehicle)
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="info-label">Véhicule</div>
                                                <div class="info-value">
                                                    {{-- Lien vers show véhicule - contrôlé par permission VIEW sur véhicules --}}
                                                    @can('vehicles.general.view')
                                                        <a href="{{ route('backoffice.vehicles.show', $booking->vehicle_id) }}">
                                                            {{ $booking->vehicle->registration_number }}
                                                        </a>
                                                    @else
                                                        {{ $booking->vehicle->registration_number }}
                                                    @endcan
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-label">Modèle</div>
                                                <div class="info-value">{{ $booking->vehicle->model->name ?? 'N/C' }}</div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-label">Tarif journalier</div>
                                                <div class="info-value">{{ number_format($booking->vehicle->daily_rate, 2) ?? 'N/C' }} MAD</div>
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-muted mb-0">Aucun véhicule associé</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel 2: Dates & Lieux -->
                <div class="info-panel" id="panel2">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="ti ti-calendar me-2"></i>
                                        Dates
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-label">Date de début</div>
                                            <div class="info-value">{{ $booking->formatted_start_date }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Date de fin</div>
                                            <div class="info-value">{{ $booking->formatted_end_date }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Durée</div>
                                            <div class="info-value">{{ $booking->booked_days }} jour(s)</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="ti ti-map-pin me-2"></i>
                                        Lieux
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-label">Prise en charge</div>
                                            <div class="info-value">{{ $booking->pickup_location }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Restitution</div>
                                            <div class="info-value">{{ $booking->dropoff_location }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel 3: Informations -->
                <div class="info-panel" id="panel3">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="ti ti-currency-dollar me-2"></i>
                                        Montant estimé
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="amount-display">{{ $booking->formatted_estimated_total }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="ti ti-info-circle me-2"></i>
                                        Informations supplémentaires
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-label">Source</div>
                                            <div class="info-value">
                                                <i class="{{ $booking->source_icon }} me-1"></i>
                                                {{ $booking->source_text }}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Statut</div>
                                            <div class="info-value">
                                                <span class="badge {{ $booking->status_badge_class }}">
                                                    {{ $booking->status_text }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Dernière modification</div>
                                            <div class="info-value">{{ $booking->updated_at->format('d/m/Y H:i') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Convert to Contract Button (if pending) - contrôlé par permission EDIT -->
                @if($booking->status == 'pending' && isset($permissions['can_edit']) && $permissions['can_edit'])
                <div class="card mb-4 border-primary">
                    <div class="card-body text-center">
                        <h5 class="mb-3">Convertir cette réservation en contrat</h5>
                        <p class="text-muted mb-3">Une fois convertie, la réservation sera marquée comme "Convertie" et vous serez redirigé vers la création d'un contrat.</p>
                        <form action="{{ route('backoffice.bookings.convert-to-contract', $booking) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="ti ti-file-text me-2"></i>Convertir en contrat
                            </button>
                        </form>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Panel Navigation
    const panels = document.querySelectorAll('.nav-link[data-panel]');
    const infoPanels = document.querySelectorAll('.info-panel');
    
    function showPanel(panelNumber) {
        infoPanels.forEach(p => p.classList.remove('active'));
        document.getElementById(`panel${panelNumber}`).classList.add('active');
        
        panels.forEach(p => p.classList.remove('active'));
        document.querySelector(`.nav-link[data-panel="${panelNumber}"]`).classList.add('active');
    }

    panels.forEach(panel => {
        panel.addEventListener('click', function(e) {
            e.preventDefault();
            showPanel(this.getAttribute('data-panel'));
        });
    });
});
</script>

@include('backoffice.bookings.partials._modal_delete')
@endsection