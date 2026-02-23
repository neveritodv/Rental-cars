<?php $page = 'invoice-item-details'; ?>
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

.amount-large {
    font-size: 1.5rem;
    font-weight: 600;
    color: #198754;
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
            <div class="col-lg-8">

                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <a href="{{ route('backoffice.invoice-items.index') }}"
                        class="d-inline-flex align-items-center fw-medium">
                        <i class="ti ti-arrow-left me-1"></i> Retour à la liste
                    </a>
                    <div>
                        <a href="{{ route('backoffice.invoice-items.edit', $invoiceItem) }}" class="btn btn-primary">
                            <i class="ti ti-edit me-1"></i>Modifier
                        </a>
                    </div>
                </div>

                <!-- Header Card -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-lg me-3"
                                    style="border-radius: 10px; background-color: #f0f3f8;">
                                    <span class="avatar-title fw-bold fs-24 text-primary">
                                        <i class="ti ti-file-description"></i>
                                    </span>
                                </span>
                                <div>
                                    <h4 class="mb-1">{{ $invoiceItem->description }}</h4>
                                    <p class="mb-0 text-muted">
                                        <i class="ti ti-file-invoice me-1"></i>
                                        Facture: <a
                                            href="{{ route('backoffice.invoices.show', $invoiceItem->invoice_id) }}">{{ $invoiceItem->invoice->invoice_number }}</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Details -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Détails</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="info-label">Description</div>
                                        <div class="info-value">{{ $invoiceItem->description }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-label">Nombre de jours</div>
                                        <div class="info-value">{{ $invoiceItem->days_count ?? '—' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-label">Quantité</div>
                                        <div class="info-value">{{ number_format($invoiceItem->quantity, 2, ',', ' ') }}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-label">Prix unitaire TTC</div>
                                        <div class="info-value">
                                            {{ $invoiceItem->unit_price_ttc ? number_format($invoiceItem->unit_price_ttc, 2, ',', ' ') . ' ' . $invoiceItem->invoice->currency : '—' }}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-label">Taux TVA</div>
                                        <div class="info-value">
                                            {{ $invoiceItem->vat_rate ? $invoiceItem->vat_rate . '%' : '—' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Montants</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <div class="info-label">Total HT</div>
                                        <div class="amount-large">
                                            {{ number_format($invoiceItem->total_ht, 2, ',', ' ') }}
                                            {{ $invoiceItem->invoice->currency }}</div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="info-label">TVA</div>
                                        <div class="amount-large">
                                            {{ number_format($invoiceItem->total_ttc - $invoiceItem->total_ht, 2, ',', ' ') }}
                                            {{ $invoiceItem->invoice->currency }}</div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="info-label">Total TTC</div>
                                        <div class="amount-large">
                                            {{ number_format($invoiceItem->total_ttc, 2, ',', ' ') }}
                                            {{ $invoiceItem->invoice->currency }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Invoice Summary -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Récapitulatif de la facture</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="info-label">N° Facture</div>
                                <div class="info-value">
                                    <a href="{{ route('backoffice.invoices.show', $invoiceItem->invoice_id) }}">
                                        {{ $invoiceItem->invoice->invoice_number }}
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-label">Date</div>
                                <div class="info-value">{{ $invoiceItem->invoice->formatted_invoice_date }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-label">Client</div>
                                <div class="info-value">
                                    @if($invoiceItem->invoice->client)
                                    <a href="{{ route('backoffice.clients.show', $invoiceItem->invoice->client_id) }}">
                                        {{ $invoiceItem->invoice->client->first_name }}
                                        {{ $invoiceItem->invoice->client->last_name }}
                                    </a>
                                    @else
                                    —
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-label">Statut</div>
                                <div class="info-value">
                                    <span class="badge {{ $invoiceItem->invoice->status_badge_class }}">
                                        {{ $invoiceItem->invoice->status_text }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@include('backoffice.invoice-items.partials._modal_delete')
@endsection