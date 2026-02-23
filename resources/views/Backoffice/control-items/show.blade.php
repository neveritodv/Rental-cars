<?php $page = 'control-item-details'; ?>
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
    .badge-success { background: #d4edda; color: #155724; }
    .badge-danger { background: #f8d7da; color: #721c24; }
    .badge-secondary { background: #e2e3e5; color: #383d41; }
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
    .status-badge-large {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1.25rem;
        font-size: 1.1rem;
        font-weight: 700;
        border-radius: 50px;
    }
    .status-badge-large i {
        font-size: 1.2rem;
    }
</style>

<div class="page-wrapper">
    <div class="content me-0">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <a href="{{ route('backoffice.control-items.index') }}" class="d-inline-flex align-items-center fw-medium">
                        <i class="ti ti-arrow-left me-1"></i> Retour à la liste
                    </a>
                    <div>
                        <a href="{{ route('backoffice.control-items.edit', $item) }}" class="btn btn-primary me-2">
                            <i class="ti ti-edit me-1"></i>Modifier
                        </a>
                    </div>
                </div>

                <!-- Header Card -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-lg me-3" style="border-radius: 10px; background-color: #f0f3f8;">
                                    <span class="avatar-title fw-bold fs-24 text-primary">
                                        <i class="ti ti-checklist"></i>
                                    </span>
                                </span>
                                <div>
                                    <h4 class="mb-1">Élément: {{ $item->item_key }}</h4>
                                    <p class="mb-0 text-muted">
                                        <i class="ti ti-calendar me-1"></i>
                                        Créé le {{ $item->created_at->format('d/m/Y à H:i') }}
                                    </p>
                                </div>
                            </div>
                            <div>
                                <span class="badge {{ $item->status_badge_class }} status-badge-large">
                                    <i class="{{ $item->status_icon }}"></i>
                                    {{ $item->status_text }}
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
                            <i class="ti ti-notes"></i>
                            Commentaire
                        </a>
                    </div>
                </div>

                <!-- Panel 1: Informations -->
                <div class="info-panel active" id="panel1">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-label">Clé</div>
                                    <div class="info-value">{{ $item->item_key }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Libellé</div>
                                    <div class="info-value">{{ $item->label ?? 'Non défini' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Contrôle associé</div>
                                    <div class="info-value">
                                        @if($item->vehicleControl)
                                            <a href="{{ route('backoffice.controls.show', $item->vehicleControl) }}">
                                                {{ $item->vehicleControl->control_number }}
                                            </a>
                                            <br>
                                            <small class="text-muted">
                                                Véhicule: {{ $item->vehicleControl->vehicle->registration_number ?? 'N/A' }}
                                            </small>
                                        @else
                                            <span class="text-muted">Non associé</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Statut</div>
                                    <div class="info-value">
                                        <span class="badge {{ $item->status_badge_class }}" style="font-size: 0.9rem; padding: 0.4rem 0.8rem;">
                                            <i class="{{ $item->status_icon }} me-1"></i>
                                            {{ $item->status_text }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel 2: Commentaire -->
                <div class="info-panel" id="panel2">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Commentaire</h5>
                        </div>
                        <div class="card-body">
                            @if($item->comment)
                                <p class="mb-0">{{ $item->comment }}</p>
                            @else
                                <p class="text-muted text-center py-3">Aucun commentaire pour cet élément</p>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@include('Backoffice.control-items.partials._modal_delete')

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