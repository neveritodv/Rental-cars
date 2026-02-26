<?php $page = 'vehicle-control-details'; ?>
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
    .badge-completed { 
        background: #d4edda; 
        color: #155724; 
        padding: 0.5rem 1rem;
        font-size: 1rem;
        font-weight: 700;
        border-radius: 50px;
        display: inline-block;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .badge-pending { 
        background: #fff3cd; 
        color: #856404; 
        padding: 0.5rem 1rem;
        font-size: 1rem;
        font-weight: 700;
        border-radius: 50px;
        display: inline-block;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .mileage-display {
        font-size: 1.5rem;
        font-weight: 600;
        color: #0d6efd;
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
    .vehicle-details {
        display: flex;
        flex-direction: column;
    }
    .vehicle-model-details {
        font-size: 0.9rem;
        color: #6c757d;
    }
    .status-badge {
        display: inline-block;
        padding: 0.5rem 1.25rem;
        font-size: 1.1rem;
        font-weight: 700;
        border-radius: 50px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .header-status {
        font-size: 1.2rem;
        font-weight: 700;
        padding: 0.6rem 1.5rem;
    }
</style>

<div class="page-wrapper">
    <div class="content me-0">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <a href="{{ route('backoffice.controls.index') }}" class="d-inline-flex align-items-center fw-medium">
                        <i class="ti ti-arrow-left me-1"></i> Retour à la liste
                    </a>
                    <div>
                        {{-- Bouton Modifier - contrôlé par permission EDIT --}}
                        @if(isset($permissions['can_edit']) && $permissions['can_edit'])
                        <a href="{{ route('backoffice.controls.edit', $control) }}" class="btn btn-primary me-2">
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
                                        <i class="ti ti-clipboard-list"></i>
                                    </span>
                                </span>
                                <div>
                                    <h4 class="mb-1">Contrôle #{{ $control->control_number }}</h4>
                                    <p class="mb-0 text-muted">
                                        <i class="ti ti-calendar me-1"></i>
                                        Créé le {{ $control->created_at->format('d/m/Y à H:i') }}
                                    </p>
                                </div>
                            </div>
                            <div>
                                <span class="badge {{ $control->status_badge_class }} header-status">
                                    {{ $control->status_text }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info Tabs -->
                <div class="wizard-nav">
                    <div class="nav-item">
                        <a class="nav-link active" data-panel="1">
                            <i class="ti ti-info-circle"></i>
                            Informations
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link" data-panel="2">
                            <i class="ti ti-speedometer"></i>
                            Kilométrage
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link" data-panel="3">
                            <i class="ti ti-notes"></i>
                            Notes
                        </a>
                    </div>
                </div>

                <!-- Panel 1: Informations -->
                <div class="info-panel active" id="panel1">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-label">Numéro de contrôle</div>
                                    <div class="info-value">{{ $control->control_number }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Statut</div>
                                    <div class="info-value">
                                        <span class="badge {{ $control->status_badge_class }}" style="font-size: 1rem; font-weight: 700; padding: 0.5rem 1rem;">
                                            {{ $control->status_text }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Véhicule</div>
                                    <div class="info-value">
                                        @php
                                            $vehicleRegNumber = 'N/A';
                                            $vehicleDetails = '';
                                            
                                            if ($control->vehicle) {
                                                $vehicleRegNumber = $control->vehicle->registration_number ?? 'N/A';
                                                
                                                // Get brand and model through relationships
                                                if ($control->vehicle->relationLoaded('model') && $control->vehicle->model) {
                                                    $brandName = $control->vehicle->model->brand->name ?? '';
                                                    $modelName = $control->vehicle->model->name ?? '';
                                                    if ($brandName || $modelName) {
                                                        $vehicleDetails = trim($brandName . ' ' . $modelName);
                                                    }
                                                }
                                            }
                                        @endphp
                                        <div class="vehicle-details">
                                            {{-- Lien vers véhicule - contrôlé par permission VIEW sur véhicules --}}
                                            @can('vehicles.general.view')
                                                <a href="{{ $control->vehicle ? route('backoffice.vehicles.show', $control->vehicle) : '#' }}" class="fw-medium">
                                                    {{ $vehicleRegNumber }}
                                                </a>
                                            @else
                                                <span class="fw-medium">{{ $vehicleRegNumber }}</span>
                                            @endcan
                                            @if($vehicleDetails)
                                                <small class="vehicle-model-details">{{ $vehicleDetails }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Contrat de location</div>
                                    <div class="info-value">
                                        @if($control->rentalContract)
                                            @php
                                                $contractNumber = $control->rentalContract->contract_number ?? 'N°' . $control->rentalContract->id;
                                            @endphp
                                            {{-- Lien vers contrat - contrôlé par permission VIEW sur contrats --}}
                                            @can('rental-contracts.general.view')
                                                <a href="{{ route('backoffice.rental-contracts.show', $control->rentalContract) }}">
                                                    #{{ $contractNumber }}
                                                </a>
                                            @else
                                                <span>#{{ $contractNumber }}</span>
                                            @endcan
                                        @else
                                            <span class="text-muted">Non associé</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Agent responsable</div>
                                    <div class="info-value">
                                        @if($control->performer)
                                            {{ $control->performer->name ?? 'N/A' }}
                                        @else
                                            <span class="text-muted">Non spécifié</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Agence</div>
                                    <div class="info-value">{{ $control->agency->name ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel 2: Kilométrage -->
                <div class="info-panel" id="panel2">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Départ</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mileage-display">{{ number_format($control->start_mileage, 0, ',', ' ') }} KM</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Arrivée</h5>
                                </div>
                                <div class="card-body">
                                    @if($control->end_mileage)
                                        <div class="mileage-display">{{ number_format($control->end_mileage, 0, ',', ' ') }} KM</div>
                                    @else
                                        <div class="text-muted">Non renseigné</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Distance parcourue</h5>
                                </div>
                                <div class="card-body">
                                    @if($control->total_distance)
                                        <div class="mileage-display">{{ number_format($control->total_distance, 0, ',', ' ') }} KM</div>
                                    @else
                                        <div class="text-muted">Non disponible</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel 3: Notes -->
                <div class="info-panel" id="panel3">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Notes et observations</h5>
                        </div>
                        <div class="card-body">
                            @if($control->notes)
                                <p class="mb-0">{{ $control->notes }}</p>
                            @else
                                <p class="text-muted text-center py-3">Aucune note pour ce contrôle</p>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@include('Backoffice.controls.partials._modal_delete')

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
@endsection