<?php $page = 'finance-account-details'; ?>
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
    .badge-info { background: #cce5ff; color: #004085; }
    .badge-success { background: #d4edda; color: #155724; }
    .badge-secondary { background: #e2e3e5; color: #383d41; }
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
    .default-badge {
        background: #ffc107;
        color: #856404;
        padding: 0.35rem 0.75rem;
        border-radius: 50px;
        font-weight: 500;
    }
</style>

<div class="page-wrapper">
    <div class="content me-0">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <a href="{{ route('backoffice.finance.accounts.index') }}" class="d-inline-flex align-items-center fw-medium">
                        <i class="ti ti-arrow-left me-1"></i> Retour à la liste
                    </a>
                    <div>
                        {{-- Bouton Modifier - contrôlé par permission EDIT --}}
                        @can('financial-accounts.general.edit')
                            <a href="{{ route('backoffice.finance.accounts.edit', $financialAccount) }}" class="btn btn-primary">
                                <i class="ti ti-edit me-1"></i>Modifier
                            </a>
                        @endcan
                    </div>
                </div>

                <!-- Header Card -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-lg me-3" style="border-radius: 10px; background-color: #f0f3f8;">
                                    <span class="avatar-title fw-bold fs-24 text-primary">
                                        <i class="ti ti-building-bank"></i>
                                    </span>
                                </span>
                                <div>
                                    <h4 class="mb-1">{{ $financialAccount->name }}</h4>
                                    <p class="mb-0 text-muted">
                                        <i class="ti ti-calendar me-1"></i>
                                        Créé le {{ $financialAccount->created_at->format('d/m/Y à H:i') }}
                                    </p>
                                </div>
                            </div>
                            <div>
                                <span class="badge {{ $financialAccount->type_badge_class }} fs-6 p-2">
                                    {{ $financialAccount->type_text }}
                                </span>
                                @if($financialAccount->is_default)
                                    <span class="default-badge ms-2">
                                        <i class="ti ti-star me-1"></i>Par défaut
                                    </span>
                                @endif
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
                            <i class="ti ti-currency-dollar"></i>
                            Solde
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link" data-panel="3">
                            <i class="ti ti-history"></i>
                            Transactions
                        </a>
                    </div>
                </div>

                <!-- Panel 1: Informations -->
                <div class="info-panel active" id="panel1">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-label">Nom du compte</div>
                                    <div class="info-value">{{ $financialAccount->name }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Type</div>
                                    <div class="info-value">
                                        <span class="badge {{ $financialAccount->type_badge_class }}">
                                            {{ $financialAccount->type_text }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">RIB / IBAN</div>
                                    <div class="info-value">{{ $financialAccount->rib ?? 'Non renseigné' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Compte par défaut</div>
                                    <div class="info-value">
                                        @if($financialAccount->is_default)
                                            <span class="default-badge">Oui</span>
                                        @else
                                            Non
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel 2: Solde -->
                <div class="info-panel" id="panel2">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Solde initial</h5>
                                </div>
                                <div class="card-body">
                                    <div class="amount-display">{{ $financialAccount->formatted_initial_balance }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Solde actuel</h5>
                                </div>
                                <div class="card-body">
                                    <div class="amount-display">{{ $financialAccount->formatted_current_balance }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel 3: Transactions -->
                <div class="info-panel" id="panel3">
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Dernières transactions</h5>
                            @can('financial-accounts.general.view')
                                <a href="{{ route('backoffice.finance.transactions.index', ['account_id' => $financialAccount->id]) }}" class="btn btn-sm btn-primary">
                                    Voir toutes
                                </a>
                            @endcan
                        </div>
                        <div class="card-body">
                            @if($financialAccount->transactions->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Description</th>
                                                <th>Catégorie</th>
                                                <th>Montant</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($financialAccount->transactions as $transaction)
                                            <tr>
                                                <td>{{ $transaction->formatted_date }}</td>
                                                <td>{{ $transaction->description ?? '—' }}</td>
                                                <td>{{ $transaction->category->name ?? '—' }}</td>
                                                <td>
                                                    <span class="{{ $transaction->type === 'income' ? 'text-success' : 'text-danger' }}">
                                                        {{ $transaction->formatted_amount }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted text-center py-3">Aucune transaction pour ce compte</p>
                            @endif
                        </div>
                    </div>
                </div>

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

@include('backoffice.finance.accounts.partials._modal_delete')
@endsection