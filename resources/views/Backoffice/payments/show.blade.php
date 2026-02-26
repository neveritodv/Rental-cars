<?php $page = 'payment-details'; ?>
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
    
    /* Method badges - LARGER AND BOLDER */
    .badge-cash, .badge-card, .badge-bank_transfer, .badge-cheque, .badge-other,
    .badge-pending, .badge-confirmed, .badge-refunded {
        padding: 0.5rem 1.2rem !important;
        border-radius: 50px !important;
        font-weight: 600 !important;
        font-size: 0.95rem !important;
        display: inline-block !important;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    /* Individual colors */
    .badge-cash { 
        background: #d4edda; 
        color: #155724; 
    }
    .badge-card { 
        background: #cce5ff; 
        color: #004085; 
    }
    .badge-bank_transfer { 
        background: #d1ecf1; 
        color: #0c5460; 
    }
    .badge-cheque { 
        background: #fff3cd; 
        color: #856404; 
    }
    .badge-other { 
        background: #e2e3e5; 
        color: #383d41; 
    }
    .badge-pending { 
        background: #fff3cd; 
        color: #856404; 
    }
    .badge-confirmed { 
        background: #d4edda; 
        color: #155724; 
    }
    .badge-refunded { 
        background: #f8d7da; 
        color: #721c24; 
    }
    
    .amount-large {
        font-size: 2rem;
        font-weight: 700;
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
    
    .btn-icon {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        color: #6c757d;
        background: transparent;
        border: 1px solid transparent;
        transition: all 0.2s;
        cursor: pointer;
    }
    
    .btn-icon:hover {
        background: #f8f9fa;
        border-color: #dee2e6;
        color: #0d6efd;
    }
    
    .btn-icon i {
        font-size: 18px;
    }
</style>

<div class="page-wrapper">
    <div class="content me-0">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <a href="{{ route('backoffice.payments.index') }}" class="d-inline-flex align-items-center fw-medium">
                        <i class="ti ti-arrow-left me-1"></i> Retour à la liste
                    </a>
                    <div class="d-flex gap-2">
                        {{-- Bouton Confirmer - contrôlé par permission EDIT --}}
                        @if(isset($permissions['can_confirm']) && $permissions['can_confirm'] && $payment->status == 'pending')
                        <form action="{{ route('backoffice.payments.status', $payment) }}" method="POST" style="display: inline;">
                            @csrf
                            <input type="hidden" name="status" value="confirmed">
                            <button type="submit" class="btn btn-success" onclick="return confirm('Confirmer ce paiement ?')">
                                <i class="ti ti-check me-1"></i>Confirmer
                            </button>
                        </form>
                        @endif
                        
                        {{-- Bouton Modifier - contrôlé par permission EDIT --}}
                        @if(isset($permissions['can_edit']) && $permissions['can_edit'])
                        <a href="{{ route('backoffice.payments.edit', $payment) }}" class="btn btn-primary">
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
                                        <i class="ti ti-currency-dollar"></i>
                                    </span>
                                </span>
                                <div>
                                    <h4 class="mb-1">Paiement #{{ $payment->id }}</h4>
                                    <p class="mb-0 text-muted">
                                        <i class="ti ti-calendar me-1"></i>
                                        Date: {{ $payment->formatted_payment_date }}
                                        @if($payment->reference)
                                        <span class="ms-3"><i class="ti ti-hash me-1"></i>Réf: {{ $payment->reference }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <span class="badge-{{ $payment->method }}">
                                    {{ $payment->method_text }}
                                </span>
                                <span class="badge-{{ $payment->status }}">
                                    {{ $payment->status_text }}
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
                            <i class="ti ti-file-invoice"></i>
                            Documents associés
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link" data-panel="3">
                            <i class="ti ti-building-bank"></i>
                            Compte
                        </a>
                    </div>
                </div>

                <!-- Panel 1: Informations -->
                <div class="info-panel active" id="panel1">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Informations générales</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-label">Montant</div>
                                            <div class="amount-large">{{ $payment->formatted_amount }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Devise</div>
                                            <div class="info-value">{{ $payment->currency }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Mode de paiement</div>
                                            <div class="info-value">
                                                <span class="badge-{{ $payment->method }}">
                                                    {{ $payment->method_text }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Statut</div>
                                            <div class="info-value">
                                                <span class="badge-{{ $payment->status }}">
                                                    {{ $payment->status_text }}
                                                </span>
                                            </div>
                                        </div>
                                        @if($payment->reference)
                                        <div class="col-md-12">
                                            <div class="info-label">Référence</div>
                                            <div class="info-value">{{ $payment->reference }}</div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Dates</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-label">Date de paiement</div>
                                            <div class="info-value">{{ $payment->formatted_payment_date }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Date de création</div>
                                            <div class="info-value">{{ $payment->created_at->format('d/m/Y H:i') }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Dernière modification</div>
                                            <div class="info-value">{{ $payment->updated_at->format('d/m/Y H:i') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel 2: Documents associés -->
                <div class="info-panel" id="panel2">
                    <div class="row">
                        @if($payment->invoice)
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Facture associée</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="info-label">N° Facture</div>
                                            <div class="info-value">
                                                {{-- Lien vers facture - contrôlé par permission VIEW sur factures --}}
                                                @can('invoices.general.view')
                                                    <a href="{{ route('backoffice.invoices.show', $payment->invoice_id) }}">
                                                        {{ $payment->invoice->invoice_number }}
                                                    </a>
                                                @else
                                                    <span>{{ $payment->invoice->invoice_number }}</span>
                                                @endcan
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Date</div>
                                            <div class="info-value">{{ $payment->invoice->formatted_invoice_date }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Montant TTC</div>
                                            <div class="info-value">{{ $payment->invoice->formatted_total_ttc }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Statut</div>
                                            <div class="info-value">
                                                <span class="badge-{{ $payment->invoice->status }}">
                                                    {{ $payment->invoice->status_text }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($payment->rentalContract)
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Contrat associé</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="info-label">N° Contrat</div>
                                            <div class="info-value">
                                                {{-- Lien vers contrat - contrôlé par permission VIEW sur contrats --}}
                                                @can('rental-contracts.general.view')
                                                    <a href="{{ route('backoffice.rental-contracts.show', $payment->rental_contract_id) }}">
                                                        {{ $payment->rentalContract->contract_number }}
                                                    </a>
                                                @else
                                                    <span>{{ $payment->rentalContract->contract_number }}</span>
                                                @endcan
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Client</div>
                                            <div class="info-value">
                                                {{-- Lien vers client - contrôlé par permission VIEW sur clients --}}
                                                @can('clients.general.view')
                                                    <a href="{{ route('backoffice.clients.show', $payment->rentalContract->client_id) }}">
                                                        {{ $payment->rentalContract->client->first_name ?? '' }} {{ $payment->rentalContract->client->last_name ?? '' }}
                                                    </a>
                                                @else
                                                    <span>{{ $payment->rentalContract->client->first_name ?? '' }} {{ $payment->rentalContract->client->last_name ?? '' }}</span>
                                                @endcan
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Montant</div>
                                            <div class="info-value">{{ $payment->rentalContract->formatted_total_amount }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(!$payment->invoice && !$payment->rentalContract)
                        <div class="col-12">
                            <div class="card mb-4">
                                <div class="card-body text-center py-5">
                                    <i class="ti ti-file-unknown fs-48 text-gray-4 mb-3"></i>
                                    <h6>Aucun document associé</h6>
                                    <p class="text-muted">Ce paiement n'est lié à aucune facture ou contrat</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Panel 3: Compte -->
                <div class="info-panel" id="panel3">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Compte financier</h5>
                        </div>
                        <div class="card-body">
                            @if($payment->financialAccount)
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-label">Nom du compte</div>
                                        <div class="info-value">
                                            {{-- Lien vers compte - contrôlé par permission VIEW sur comptes --}}
                                            @can('financial-accounts.general.view')
                                                <a href="{{ route('backoffice.finance.accounts.show', $payment->financial_account_id) }}">
                                                    {{ $payment->financialAccount->name }}
                                                </a>
                                            @else
                                                <span>{{ $payment->financialAccount->name }}</span>
                                            @endcan
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-label">Type</div>
                                        <div class="info-value">
                                            <span class="badge-{{ $payment->financialAccount->type }}">
                                                {{ $payment->financialAccount->type_text }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-label">Solde actuel</div>
                                        <div class="info-value">{{ $payment->financialAccount->formatted_current_balance }}</div>
                                    </div>
                                    @if($payment->financialAccount->rib)
                                    <div class="col-md-6">
                                        <div class="info-label">RIB</div>
                                        <div class="info-value">{{ $payment->financialAccount->rib }}</div>
                                    </div>
                                    @endif
                                </div>
                            @else
                                <p class="text-muted">Compte non trouvé</p>
                            @endif
                        </div>
                    </div>

                    @if($payment->financialTransaction)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Transaction financière associée</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-label">ID Transaction</div>
                                    <div class="info-value">#{{ $payment->financialTransaction->id }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Date</div>
                                    <div class="info-value">{{ $payment->financialTransaction->date->format('d/m/Y') }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Montant</div>
                                    <div class="info-value">{{ $payment->financialTransaction->formatted_amount }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Description</div>
                                    <div class="info-value">{{ $payment->financialTransaction->description ?? '—' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
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

@include('backoffice.payments.partials._modal_delete')
@endsection