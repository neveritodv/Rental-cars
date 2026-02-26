<?php $page = 'client-details'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<style>
    .document-card {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
        border-left: 4px solid #0d6efd;
    }
    .document-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        cursor: pointer;
        transition: transform 0.3s;
    }
    .document-image:hover {
        transform: scale(1.05);
    }
    .document-icon {
        font-size: 3rem;
        color: #6c757d;
    }
    .info-label {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }
    .info-value {
        font-weight: 500;
        margin-bottom: 1rem;
    }
    .avatar-large {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #fff;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .document-thumbnail {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 8px;
        cursor: pointer;
        border: 1px solid #dee2e6;
        transition: transform 0.3s;
    }
    .document-thumbnail:hover {
        transform: scale(1.1);
    }
</style>

<div class="page-wrapper">
    <div class="content me-0">
        <div class="row">
            <div class="col-lg-12">

                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <a href="{{ route('backoffice.clients.index') }}" class="d-inline-flex align-items-center fw-medium">
                        <i class="ti ti-arrow-left me-1"></i> Retour à la liste
                    </a>
                    <div>
                        {{-- Bouton Modifier - contrôlé par permission EDIT --}}
                        @if(isset($permissions['can_edit']) && $permissions['can_edit'])
                        <a href="{{ route('backoffice.clients.edit', $client) }}" class="btn btn-primary">
                            <i class="ti ti-edit me-1"></i>Modifier
                        </a>
                        @endif
                    </div>
                </div>

                <!-- Header Card -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2 text-center">
                                @if($client->getFirstMediaUrl('avatar'))
                                    <img src="{{ $client->getFirstMediaUrl('avatar') }}" 
                                         alt="{{ $client->first_name }} {{ $client->last_name }}" 
                                         class="avatar-large">
                                @else
                                    <div class="avatar-large bg-primary text-white d-flex align-items-center justify-content-center" 
                                         style="font-size: 3rem; border-radius: 50%; margin: 0 auto;">
                                        {{ strtoupper(substr($client->first_name, 0, 1) . substr($client->last_name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <h3 class="mb-2">{{ $client->first_name }} {{ $client->last_name }}</h3>
                                <p class="text-muted mb-1">
                                    <i class="ti ti-mail me-2"></i>{{ $client->email ?? 'Non renseigné' }}
                                </p>
                                <p class="text-muted mb-1">
                                    <i class="ti ti-phone me-2"></i>{{ $client->phone }}
                                </p>
                                <p class="text-muted mb-0">
                                    <i class="ti ti-map-pin me-2"></i>{{ $client->address ?? 'Adresse non renseignée' }}
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                @php
                                    $statusColors = [
                                        'active' => 'success',
                                        'inactive' => 'secondary',
                                        'blacklisted' => 'danger'
                                    ];
                                    $statusTexts = [
                                        'active' => 'Actif',
                                        'inactive' => 'Inactif',
                                        'blacklisted' => 'Blacklisté'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$client->status] ?? 'secondary' }} fs-6 p-2">
                                    {{ $statusTexts[$client->status] ?? $client->status }}
                                </span>
                                <p class="mt-2 text-muted">
                                    <i class="ti ti-calendar me-1"></i>
                                    Membre depuis {{ $client->created_at->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations Personnelles -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-user me-2"></i>
                            Informations personnelles
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="info-label">Agence</div>
                                <div class="info-value">{{ $client->agency->name ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-label">Date de naissance</div>
                                <div class="info-value">{{ $client->birth_date ? $client->birth_date->format('d/m/Y') : 'Non renseignée' }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-label">Nationalité</div>
                                <div class="info-value">{{ $client->nationality ?? 'Non renseignée' }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-label">Téléphone</div>
                                <div class="info-value">{{ $client->phone }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Adresse -->
                @if($client->address || $client->city || $client->country)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-map-pin me-2"></i>
                            Adresse
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if($client->address)
                            <div class="col-md-4">
                                <div class="info-label">Adresse</div>
                                <div class="info-value">{{ $client->address }}</div>
                            </div>
                            @endif
                            @if($client->city)
                            <div class="col-md-3">
                                <div class="info-label">Ville</div>
                                <div class="info-value">{{ $client->city }}</div>
                            </div>
                            @endif
                            @if($client->country)
                            <div class="col-md-3">
                                <div class="info-label">Pays</div>
                                <div class="info-value">{{ $client->country }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Documents d'identité -->
                @if($client->cin_number || $client->getFirstMedia('cin_front') || $client->getFirstMedia('cin_back') ||
                    $client->passport_number || $client->getFirstMedia('passport') ||
                    $client->driving_license_number || $client->getFirstMedia('license'))
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-id me-2"></i>
                            Documents d'identité
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- CIN -->
                            @if($client->cin_number || $client->getFirstMedia('cin_front') || $client->getFirstMedia('cin_back'))
                            <div class="col-md-6">
                                <div class="document-card">
                                    <h6 class="mb-3">
                                        <i class="ti ti-card me-2"></i>
                                        Carte d'Identité Nationale (CIN)
                                    </h6>
                                    @if($client->cin_number)
                                    <p><strong>Numéro:</strong> {{ $client->cin_number }}</p>
                                    @endif
                                    @if($client->cin_valid_until)
                                    <p><strong>Valide jusqu'au:</strong> {{ $client->cin_valid_until->format('d/m/Y') }}</p>
                                    @endif
                                    <div class="row mt-3">
                                        @if($client->getFirstMedia('cin_front'))
                                        <div class="col-6 text-center">
                                            <img src="{{ $client->getFirstMediaUrl('cin_front') }}" 
                                                 class="document-thumbnail" 
                                                 alt="Recto CIN"
                                                 onclick="openImageModal('{{ $client->getFirstMediaUrl('cin_front') }}', 'Recto CIN')">
                                            <p class="mt-1"><small>Recto</small></p>
                                        </div>
                                        @endif
                                        @if($client->getFirstMedia('cin_back'))
                                        <div class="col-6 text-center">
                                            <img src="{{ $client->getFirstMediaUrl('cin_back') }}" 
                                                 class="document-thumbnail" 
                                                 alt="Verso CIN"
                                                 onclick="openImageModal('{{ $client->getFirstMediaUrl('cin_back') }}', 'Verso CIN')">
                                            <p class="mt-1"><small>Verso</small></p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Passeport -->
                            @if($client->passport_number || $client->getFirstMedia('passport'))
                            <div class="col-md-6">
                                <div class="document-card">
                                    <h6 class="mb-3">
                                        <i class="ti ti-book me-2"></i>
                                        Passeport
                                    </h6>
                                    @if($client->passport_number)
                                    <p><strong>Numéro:</strong> {{ $client->passport_number }}</p>
                                    @endif
                                    @if($client->passport_issue_date)
                                    <p><strong>Délivré le:</strong> {{ $client->passport_issue_date->format('d/m/Y') }}</p>
                                    @endif
                                    @if($client->getFirstMedia('passport'))
                                    <div class="mt-3 text-center">
                                        <img src="{{ $client->getFirstMediaUrl('passport') }}" 
                                             class="document-thumbnail" 
                                             alt="Passeport"
                                             onclick="openImageModal('{{ $client->getFirstMediaUrl('passport') }}', 'Passeport')">
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif

                            <!-- Permis de Conduire -->
                            @if($client->driving_license_number || $client->getFirstMedia('license'))
                            <div class="col-md-6">
                                <div class="document-card">
                                    <h6 class="mb-3">
                                        <i class="ti ti-car me-2"></i>
                                        Permis de Conduire
                                    </h6>
                                    @if($client->driving_license_number)
                                    <p><strong>Numéro:</strong> {{ $client->driving_license_number }}</p>
                                    @endif
                                    @if($client->driving_license_issue_date)
                                    <p><strong>Délivré le:</strong> {{ $client->driving_license_issue_date->format('d/m/Y') }}</p>
                                    @endif
                                    @if($client->getFirstMedia('license'))
                                    <div class="mt-3 text-center">
                                        <img src="{{ $client->getFirstMediaUrl('license') }}" 
                                             class="document-thumbnail" 
                                             alt="Permis de Conduire"
                                             onclick="openImageModal('{{ $client->getFirstMediaUrl('license') }}', 'Permis de Conduire')">
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Notes -->
                @if($client->notes)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-notes me-2"></i>
                            Notes
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $client->notes }}</p>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img src="" id="modalImage" class="img-fluid" style="max-height: 70vh;">
            </div>
        </div>
    </div>
</div>

<script>
    function openImageModal(imageUrl, title) {
        document.getElementById('modalImage').src = imageUrl;
        document.getElementById('imageModalTitle').textContent = title;
        new bootstrap.Modal(document.getElementById('imageModal')).show();
    }
</script>
@endsection