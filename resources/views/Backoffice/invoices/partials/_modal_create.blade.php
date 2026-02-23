<?php $page = 'invoices'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<style>
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
    .fieldset {
        display: none;
    }
    .fieldset.active {
        display: block;
    }
</style>

<div class="page-wrapper">
    <div class="content me-0">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="mb-3">
                    <a href="{{ route('backoffice.invoices.index') }}" class="d-inline-flex align-items-center fw-medium">
                        <i class="ti ti-arrow-left me-1"></i> Retour à la liste
                    </a>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="ti ti-file-invoice me-2"></i>
                            Nouvelle facture
                        </h4>
                    </div>

                    <div class="card-body">
                        <!-- Wizard Navigation -->
                        <div class="wizard-nav">
                            <div class="nav-item">
                                <a class="nav-link active" data-tab="1">
                                    <i class="ti ti-info-circle"></i>
                                    Informations
                                </a>
                            </div>
                            <div class="nav-item">
                                <a class="nav-link" data-tab="2">
                                    <i class="ti ti-currency-dollar"></i>
                                    Montants
                                </a>
                            </div>
                            <div class="nav-item">
                                <a class="nav-link" data-tab="3">
                                    <i class="ti ti-building"></i>
                                    Société
                                </a>
                            </div>
                        </div>

                        <form action="{{ route('backoffice.invoices.store') }}" method="POST" class="needs-validation" novalidate>
                            @csrf

                            <!-- Tab 1: Informations -->
                            <fieldset class="fieldset active" id="tab1">
                                <div class="row">
                                    <!-- Invoice Number (readonly) -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">N° Facture</label>
                                            <input type="text" class="form-control" value="{{ $invoiceNumber }}" readonly>
                                        </div>
                                    </div>

                                    <!-- Invoice Date -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Date facture <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" name="invoice_date" value="{{ old('invoice_date', date('Y-m-d')) }}" 
                                                   class="form-control @error('invoice_date') is-invalid @enderror" required>
                                            @error('invoice_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Client -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Client</label>
                                            <select name="client_id" class="form-select @error('client_id') is-invalid @enderror">
                                                <option value="">Sélectionner un client</option>
                                                @foreach($clients as $client)
                                                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                                        {{ $client->first_name }} {{ $client->last_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('client_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Rental Contract -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Contrat de location</label>
                                            <select name="rental_contract_id" class="form-select @error('rental_contract_id') is-invalid @enderror">
                                                <option value="">Sélectionner un contrat</option>
                                                @foreach($contracts as $contract)
                                                    <option value="{{ $contract->id }}" {{ old('rental_contract_id') == $contract->id ? 'selected' : '' }}>
                                                        {{ $contract->contract_number }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('rental_contract_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Status -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Statut <span class="text-danger">*</span>
                                            </label>
                                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                                <option value="">Sélectionner un statut</option>
                                                <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Brouillon</option>
                                                <option value="sent" {{ old('status') == 'sent' ? 'selected' : '' }}>Envoyée</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Currency -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Devise <span class="text-danger">*</span>
                                            </label>
                                            <select name="currency" class="form-select @error('currency') is-invalid @enderror" required>
                                                <option value="MAD" {{ old('currency', 'MAD') == 'MAD' ? 'selected' : '' }}>MAD</option>
                                                <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                                                <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                                            </select>
                                            @error('currency')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <button type="button" class="btn btn-primary next-tab" data-next="2">
                                        Suivant <i class="ti ti-chevron-right ms-1"></i>
                                    </button>
                                </div>
                            </fieldset>

                            <!-- Tab 2: Montants -->
                            <fieldset class="fieldset" id="tab2">
                                <div class="row">
                                    <!-- VAT Rate -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                TVA (%) <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number" name="vat_rate" id="vat_rate" value="{{ old('vat_rate', 20) }}" 
                                                       class="form-control @error('vat_rate') is-invalid @enderror" 
                                                       step="0.01" min="0" max="100" required>
                                                <span class="input-group-text">%</span>
                                            </div>
                                            @error('vat_rate')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Total HT -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Montant HT <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number" name="total_ht" id="total_ht" value="{{ old('total_ht') }}" 
                                                       class="form-control @error('total_ht') is-invalid @enderror" 
                                                       step="0.01" min="0" required>
                                                <span class="input-group-text">MAD</span>
                                            </div>
                                            @error('total_ht')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Calculated amounts (readonly) -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">TVA</label>
                                            <div class="input-group">
                                                <input type="text" id="total_vat_display" class="form-control" value="0,00" readonly>
                                                <span class="input-group-text">MAD</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Total TTC</label>
                                            <div class="input-group">
                                                <input type="text" id="total_ttc_display" class="form-control" value="0,00" readonly>
                                                <span class="input-group-text">MAD</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-light prev-tab" data-prev="1">
                                        <i class="ti ti-chevron-left me-1"></i> Précédent
                                    </button>
                                    <button type="button" class="btn btn-primary next-tab" data-next="3">
                                        Suivant <i class="ti ti-chevron-right ms-1"></i>
                                    </button>
                                </div>
                            </fieldset>

                            <!-- Tab 3: Société -->
                            <fieldset class="fieldset" id="tab3">
                                <div class="row">
                                    <!-- Company Name -->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Nom de la société</label>
                                            <input type="text" name="company_name" value="{{ old('company_name') }}" 
                                                   class="form-control @error('company_name') is-invalid @enderror" 
                                                   maxlength="150" placeholder="Si différent du client">
                                            @error('company_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Company Address -->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Adresse</label>
                                            <textarea name="company_address" class="form-control @error('company_address') is-invalid @enderror" 
                                                      rows="3" placeholder="Adresse de la société">{{ old('company_address') }}</textarea>
                                            @error('company_address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Company Phone -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Téléphone</label>
                                            <input type="text" name="company_phone" value="{{ old('company_phone') }}" 
                                                   class="form-control @error('company_phone') is-invalid @enderror" 
                                                   maxlength="50">
                                            @error('company_phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Company Email -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" name="company_email" value="{{ old('company_email') }}" 
                                                   class="form-control @error('company_email') is-invalid @enderror" 
                                                   maxlength="150">
                                            @error('company_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Notes -->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Notes</label>
                                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" 
                                                      rows="3" placeholder="Informations complémentaires...">{{ old('notes') }}</textarea>
                                            @error('notes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-light prev-tab" data-prev="2">
                                        <i class="ti ti-chevron-left me-1"></i> Précédent
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-device-floppy me-1"></i> Créer la facture
                                    </button>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab Navigation
    const tabs = document.querySelectorAll('.nav-link[data-tab]');
    const fieldsets = document.querySelectorAll('.fieldset');
    
    function showTab(tabNumber) {
        fieldsets.forEach(f => f.classList.remove('active'));
        document.getElementById(`tab${tabNumber}`).classList.add('active');
        
        tabs.forEach(t => t.classList.remove('active'));
        document.querySelector(`.nav-link[data-tab="${tabNumber}"]`).classList.add('active');
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            showTab(this.getAttribute('data-tab'));
        });
    });

    document.querySelectorAll('.next-tab').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            showTab(this.getAttribute('data-next'));
        });
    });

    document.querySelectorAll('.prev-tab').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            showTab(this.getAttribute('data-prev'));
        });
    });

    // Calculate VAT and TTC
    const vatRate = document.getElementById('vat_rate');
    const totalHt = document.getElementById('total_ht');
    const totalVatDisplay = document.getElementById('total_vat_display');
    const totalTtcDisplay = document.getElementById('total_ttc_display');

    function calculateTotals() {
        const ht = parseFloat(totalHt.value) || 0;
        const vat = parseFloat(vatRate.value) || 0;
        
        const vatAmount = ht * (vat / 100);
        const ttc = ht + vatAmount;
        
        totalVatDisplay.value = vatAmount.toFixed(2).replace('.', ',');
        totalTtcDisplay.value = ttc.toFixed(2).replace('.', ',');
    }

    if (vatRate && totalHt) {
        vatRate.addEventListener('input', calculateTotals);
        totalHt.addEventListener('input', calculateTotals);
    }

    // Bootstrap validation
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
});
</script>
@endsection