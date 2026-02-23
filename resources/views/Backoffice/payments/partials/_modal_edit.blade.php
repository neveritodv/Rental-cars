<?php $page = 'payments'; ?>
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
                    <a href="{{ route('backoffice.payments.index') }}" class="d-inline-flex align-items-center fw-medium">
                        <i class="ti ti-arrow-left me-1"></i> Retour à la liste
                    </a>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="ti ti-edit me-2"></i>
                            Modifier le paiement
                        </h4>
                        <p class="text-muted mb-0 mt-1">
                            Paiement #{{ $payment->id }} - {{ $payment->formatted_payment_date }}
                        </p>
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
                                    Montant
                                </a>
                            </div>
                            <div class="nav-item">
                                <a class="nav-link" data-tab="3">
                                    <i class="ti ti-building-bank"></i>
                                    Compte
                                </a>
                            </div>
                        </div>

                        <form action="{{ route('backoffice.payments.update', $payment) }}" method="POST" class="needs-validation" novalidate>
                            @csrf
                            @method('PUT')

                            <!-- Tab 1: Informations -->
                            <fieldset class="fieldset active" id="tab1">
                                <div class="row">
                                    <!-- Payment Date -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Date de paiement <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" name="payment_date" value="{{ old('payment_date', $payment->payment_date->format('Y-m-d')) }}" 
                                                   class="form-control @error('payment_date') is-invalid @enderror" required>
                                            @error('payment_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Reference -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Référence</label>
                                            <input type="text" name="reference" value="{{ old('reference', $payment->reference) }}" 
                                                   class="form-control @error('reference') is-invalid @enderror" 
                                                   maxlength="100">
                                            @error('reference')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Invoice -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Facture associée</label>
                                            <select name="invoice_id" class="form-select @error('invoice_id') is-invalid @enderror">
                                                <option value="">Aucune</option>
                                                @foreach($invoices as $invoice)
                                                    <option value="{{ $invoice->id }}" {{ old('invoice_id', $payment->invoice_id) == $invoice->id ? 'selected' : '' }}>
                                                        {{ $invoice->invoice_number }} - {{ $invoice->formatted_total_ttc }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('invoice_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Contract -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Contrat associé</label>
                                            <select name="rental_contract_id" class="form-select @error('rental_contract_id') is-invalid @enderror">
                                                <option value="">Aucun</option>
                                                @foreach($contracts as $contract)
                                                    <option value="{{ $contract->id }}" {{ old('rental_contract_id', $payment->rental_contract_id) == $contract->id ? 'selected' : '' }}>
                                                        {{ $contract->contract_number }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('rental_contract_id')
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

                            <!-- Tab 2: Montant -->
                            <fieldset class="fieldset" id="tab2">
                                <div class="row">
                                    <!-- Amount -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Montant <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number" name="amount" id="amount" value="{{ old('amount', $payment->amount) }}" 
                                                       class="form-control @error('amount') is-invalid @enderror" 
                                                       step="0.01" min="0.01" required>
                                                <span class="input-group-text">{{ $payment->currency }}</span>
                                            </div>
                                            @error('amount')
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
                                                <option value="MAD" {{ old('currency', $payment->currency) == 'MAD' ? 'selected' : '' }}>MAD</option>
                                                <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                                                <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                                            </select>
                                            @error('currency')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Method -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Mode de paiement <span class="text-danger">*</span>
                                            </label>
                                            <select name="method" class="form-select @error('method') is-invalid @enderror" required>
                                                <option value="">Sélectionner</option>
                                                <option value="cash" {{ old('method', $payment->method) == 'cash' ? 'selected' : '' }}>Espèces</option>
                                                <option value="card" {{ old('method', $payment->method) == 'card' ? 'selected' : '' }}>Carte bancaire</option>
                                                <option value="bank_transfer" {{ old('method', $payment->method) == 'bank_transfer' ? 'selected' : '' }}>Virement</option>
                                                <option value="cheque" {{ old('method', $payment->method) == 'cheque' ? 'selected' : '' }}>Chèque</option>
                                                <option value="other" {{ old('method', $payment->method) == 'other' ? 'selected' : '' }}>Autre</option>
                                            </select>
                                            @error('method')
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
                                                <option value="confirmed" {{ old('status', $payment->status) == 'confirmed' ? 'selected' : '' }}>Confirmé</option>
                                                <option value="pending" {{ old('status', $payment->status) == 'pending' ? 'selected' : '' }}>En attente</option>
                                                <option value="refunded" {{ old('status', $payment->status) == 'refunded' ? 'selected' : '' }}>Remboursé</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
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

                            <!-- Tab 3: Compte -->
                            <fieldset class="fieldset" id="tab3">
                                <div class="row">
                                    <!-- Financial Account -->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Compte financier <span class="text-danger">*</span>
                                            </label>
                                            <select name="financial_account_id" class="form-select @error('financial_account_id') is-invalid @enderror" required>
                                                <option value="">Sélectionner un compte</option>
                                                @foreach($accounts as $account)
                                                    <option value="{{ $account->id }}" {{ old('financial_account_id', $payment->financial_account_id) == $account->id ? 'selected' : '' }}>
                                                        {{ $account->name }} ({{ $account->formatted_current_balance }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('financial_account_id')
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
                                        <i class="ti ti-device-floppy me-1"></i> Mettre à jour
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