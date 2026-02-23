<?php $page = 'invoice-details'; ?>
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
    .badge-secondary { background: #e2e3e5; color: #383d41; }
    .badge-info { background: #cce5ff; color: #004085; }
    .badge-success { background: #d4edda; color: #155724; }
    .badge-warning { background: #fff3cd; color: #856404; }
    .badge-danger { background: #f8d7da; color: #721c24; }
    
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
    
    .amount-badge { 
        background: #e8f5e9; 
        color: #2e7d32; 
        padding: 0.35rem 0.75rem; 
        border-radius: 50px; 
        font-weight: 500; 
        white-space: nowrap;
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
    
    .dropdown-menu {
        z-index: 9999 !important;
        display: block;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s;
    }
    
    .dropdown-menu.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    /* PDF Preview Styles */
    .pdf-preview-container {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        overflow: hidden;
        margin-top: 20px;
    }
    
    .pdf-preview-header {
        background: #f8f9fa;
        padding: 12px 20px;
        border-bottom: 1px solid #dee2e6;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .pdf-preview {
        width: 100%;
        height: 600px;
        border: none;
    }
    
    .pdf-placeholder {
        background: #f8f9fa;
        padding: 60px 20px;
        text-align: center;
        border: 2px dashed #dee2e6;
        border-radius: 8px;
    }
    
    .pdf-placeholder i {
        font-size: 48px;
        color: #dc3545;
        margin-bottom: 15px;
    }
</style>

<div class="page-wrapper">
    <div class="content me-0">
        <div class="row justify-content-center">
            <div class="col-lg-12">

                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <a href="{{ route('backoffice.invoices.index') }}" class="d-inline-flex align-items-center fw-medium">
                        <i class="ti ti-arrow-left me-1"></i> Retour à la liste
                    </a>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                            <i class="ti ti-printer me-1"></i>Imprimer
                        </button>
                        <a href="{{ route('backoffice.invoices.pdf.single', $invoice->id) }}" class="btn btn-danger" target="_blank">
                            <i class="ti ti-file-text me-1"></i>Télécharger PDF
                        </a>
                        @if($invoice->client && $invoice->client->phone)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $invoice->client->phone) }}?text={{ urlencode('Bonjour, voici votre facture #' . $invoice->invoice_number . ' : ' . route('backoffice.invoices.pdf.single', $invoice->id, true)) }}" 
                           class="btn btn-success" target="_blank">
                            <i class="ti ti-brand-whatsapp me-1"></i>WhatsApp
                        </a>
                        @endif
                        <a href="{{ route('backoffice.invoices.edit', $invoice) }}" class="btn btn-primary">
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
                                        <i class="ti ti-file-invoice"></i>
                                    </span>
                                </span>
                                <div>
                                    <h4 class="mb-1">Facture {{ $invoice->invoice_number }}</h4>
                                    <p class="mb-0 text-muted">
                                        <i class="ti ti-calendar me-1"></i>
                                        Date: {{ $invoice->formatted_invoice_date }}
                                    </p>
                                </div>
                            </div>
                            <div>
                                <span class="badge {{ $invoice->status_badge_class }} fs-6 p-2">
                                    {{ $invoice->status_text }}
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
                            Détails
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link" data-panel="2">
                            <i class="ti ti-currency-dollar"></i>
                            Montants
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link" data-panel="3">
                            <i class="ti ti-building"></i>
                            Société
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link" data-panel="4">
                            <i class="ti ti-file-text"></i>
                            Aperçu PDF
                        </a>
                    </div>
                </div>

                <!-- Panel 1: Détails -->
                <div class="info-panel active" id="panel1">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">Client</h5>
                                    @if($invoice->client && $invoice->client->phone)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $invoice->client->phone) }}" 
                                       class="btn btn-sm btn-success" target="_blank">
                                        <i class="ti ti-brand-whatsapp me-1"></i>WhatsApp
                                    </a>
                                    @endif
                                </div>
                                <div class="card-body">
                                    @if($invoice->client)
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="info-label">Nom</div>
                                                <div class="info-value">
                                                    <a href="{{ route('backoffice.clients.show', $invoice->client_id) }}">
                                                        {{ $invoice->client->first_name }} {{ $invoice->client->last_name }}
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-label">Téléphone</div>
                                                <div class="info-value">
                                                    {{ $invoice->client->phone }}
                                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $invoice->client->phone) }}" 
                                                       class="ms-2 text-success" target="_blank">
                                                        <i class="ti ti-brand-whatsapp"></i>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-label">Email</div>
                                                <div class="info-value">{{ $invoice->client->email ?? '—' }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-muted">Aucun client associé</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Contrat associé</h5>
                                </div>
                                <div class="card-body">
                                    @if($invoice->rentalContract)
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="info-label">N° Contrat</div>
                                                <div class="info-value">
                                                    <a href="{{ route('backoffice.rental-contracts.show', $invoice->rental_contract_id) }}">
                                                        {{ $invoice->rentalContract->contract_number }}
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-label">Début</div>
                                                <div class="info-value">{{ $invoice->rentalContract->formatted_start_date }}</div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-label">Fin</div>
                                                <div class="info-value">{{ $invoice->rentalContract->formatted_end_date }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-muted">Aucun contrat associé</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel 2: Montants -->
                <div class="info-panel" id="panel2">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Montant HT</h5>
                                </div>
                                <div class="card-body">
                                    <div class="amount-large">{{ $invoice->formatted_total_ht }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">TVA ({{ $invoice->vat_rate }}%)</h5>
                                </div>
                                <div class="card-body">
                                    <div class="amount-large">{{ $invoice->formatted_total_vat }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Total TTC</h5>
                                </div>
                                <div class="card-body">
                                    <div class="amount-large">{{ $invoice->formatted_total_ttc }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel 3: Société -->
                <div class="info-panel" id="panel3">
                    <div class="card mb-4">
                        <div class="card-body">
                            @if($invoice->company_name || $invoice->company_address || $invoice->company_phone || $invoice->company_email)
                                <div class="row">
                                    @if($invoice->company_name)
                                    <div class="col-md-6">
                                        <div class="info-label">Nom de la société</div>
                                        <div class="info-value">{{ $invoice->company_name }}</div>
                                    </div>
                                    @endif
                                    @if($invoice->company_phone)
                                    <div class="col-md-6">
                                        <div class="info-label">Téléphone</div>
                                        <div class="info-value">{{ $invoice->company_phone }}</div>
                                    </div>
                                    @endif
                                    @if($invoice->company_email)
                                    <div class="col-md-6">
                                        <div class="info-label">Email</div>
                                        <div class="info-value">{{ $invoice->company_email }}</div>
                                    </div>
                                    @endif
                                    @if($invoice->company_address)
                                    <div class="col-md-12">
                                        <div class="info-label">Adresse</div>
                                        <div class="info-value">{{ $invoice->company_address }}</div>
                                    </div>
                                    @endif
                                </div>
                            @else
                                <p class="text-muted">Aucune information société</p>
                            @endif
                        </div>
                    </div>

                    @if($invoice->notes)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Notes</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $invoice->notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Panel 4: Aperçu PDF -->
                <div class="info-panel" id="panel4">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Aperçu PDF</h5>
                            <div class="d-flex gap-2">
                                <a href="{{ route('backoffice.invoices.pdf.single', $invoice->id) }}" class="btn btn-sm btn-danger" target="_blank">
                                    <i class="ti ti-download me-1"></i>Télécharger
                                </a>
                                @if($invoice->client && $invoice->client->phone)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $invoice->client->phone) }}?text={{ urlencode('Bonjour, voici votre facture #' . $invoice->invoice_number . ' : ' . route('backoffice.invoices.pdf.single', $invoice->id, true)) }}" 
                                   class="btn btn-sm btn-success" target="_blank">
                                    <i class="ti ti-brand-whatsapp me-1"></i>Partager
                                </a>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="pdf-preview-container">
                                <div class="pdf-preview-header">
                                    <span><i class="ti ti-file-text text-danger me-2"></i>{{ $invoice->invoice_number }}.pdf</span>
                                    <span class="text-muted small">{{ number_format($invoice->total_ttc, 2) }} {{ $invoice->currency }}</span>
                                </div>
                                <iframe src="{{ route('backoffice.invoices.pdf.view', $invoice->id) }}" class="pdf-preview"></iframe>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Invoice Items -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-file-description me-2"></i>
                            Lignes de facture
                        </h5>
                        <a href="{{ route('backoffice.invoice-items.create', ['invoice_id' => $invoice->id]) }}" class="btn btn-sm btn-primary">
                            <i class="ti ti-plus me-1"></i>Ajouter une ligne
                        </a>
                    </div>
                    <div class="card-body p-0">
                        @if($invoice->items && $invoice->items->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Description</th>
                                            <th>Jours</th>
                                            <th>Quantité</th>
                                            <th>Prix unitaire</th>
                                            <th>Total HT</th>
                                            <th>TVA</th>
                                            <th>Total TTC</th>
                                            <th width="80">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($invoice->items as $item)
                                        <tr>
                                            <td>{{ $item->description }}</td>
                                            <td>{{ $item->days_count ?? '—' }}</td>
                                            <td>{{ number_format($item->quantity, 2, ',', ' ') }}</td>
                                            <td>{{ number_format($item->unit_price, 2, ',', ' ') }} {{ $invoice->currency }}</td>
                                            <td>{{ number_format($item->total_ht, 2, ',', ' ') }} {{ $invoice->currency }}</td>
                                            <td>{{ $item->vat_rate ? $item->vat_rate . '%' : '—' }}</td>
                                            <td><span class="amount-badge">{{ number_format($item->total_ttc, 2, ',', ' ') }} {{ $invoice->currency }}</span></td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown">
                                                        <i class="ti ti-dots-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end p-2">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('backoffice.invoice-items.edit', $item) }}">
                                                                <i class="ti ti-edit me-2"></i>Modifier
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item text-danger" 
                                                               href="javascript:void(0);"
                                                               data-bs-toggle="modal" 
                                                               data-bs-target="#delete_invoice_item"
                                                               data-delete-action="{{ route('backoffice.invoice-items.destroy', $item) }}"
                                                               data-delete-details="la ligne <strong>{{ $item->description }}</strong>">
                                                                <i class="ti ti-trash me-2"></i>Supprimer
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-secondary">
                                        <tr>
                                            <td colspan="4" class="text-end"><strong>Totaux:</strong></td>
                                            <td><strong>{{ number_format($invoice->total_ht, 2, ',', ' ') }} {{ $invoice->currency }}</strong></td>
                                            <td><strong>{{ number_format($invoice->total_vat, 2, ',', ' ') }} {{ $invoice->currency }}</strong></td>
                                            <td><strong>{{ number_format($invoice->total_ttc, 2, ',', ' ') }} {{ $invoice->currency }}</strong></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="ti ti-file-description fs-48 text-gray-4 mb-3"></i>
                                <h6 class="mb-2">Aucune ligne</h6>
                                <p class="text-muted mb-3">Cette facture n'a pas encore de lignes</p>
                                <a href="{{ route('backoffice.invoice-items.create', ['invoice_id' => $invoice->id]) }}" class="btn btn-primary btn-sm">
                                    <i class="ti ti-plus me-1"></i>Ajouter une ligne
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Delete Item Modal -->
<div class="modal fade" id="delete_invoice_item" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center">
                <span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
                    <i class="ti ti-trash-x fs-26"></i>
                </span>
                <h4 class="mb-1">Supprimer la ligne</h4>
                <p class="mb-3" id="deleteInvoiceItemText">Êtes-vous sûr de vouloir supprimer cette ligne ?</p>
                
                <form method="POST" action="" id="deleteInvoiceItemForm">
                    @csrf
                    @method('DELETE')
                    
                    <div class="d-flex justify-content-center">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-danger">Oui, supprimer</button>
                    </div>
                </form>
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

    // Delete Item Modal
    const deleteModal = document.getElementById('delete_invoice_item');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            
            if (button) {
                const action = button.getAttribute('data-delete-action');
                const details = button.getAttribute('data-delete-details') || 'cette ligne';
                
                const form = document.getElementById('deleteInvoiceItemForm');
                const text = document.getElementById('deleteInvoiceItemText');
                
                if (action && form) {
                    form.action = action;
                }
                
                if (text && details) {
                    text.innerHTML = 'Êtes-vous sûr de vouloir supprimer ' + details + ' ?';
                }
            }
        });
    }
});
</script>
@endsection