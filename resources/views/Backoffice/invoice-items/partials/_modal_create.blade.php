<?php $page = 'invoice-items'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<div class="page-wrapper">
    <div class="content me-0">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="mb-3">
                    <a href="{{ route('backoffice.invoice-items.index') }}" class="d-inline-flex align-items-center fw-medium">
                        <i class="ti ti-arrow-left me-1"></i> Retour à la liste
                    </a>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="ti ti-file-description me-2"></i>
                            Nouvel item de facture
                        </h4>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('backoffice.invoice-items.store') }}" method="POST" class="needs-validation" novalidate>
                            @csrf

                            <div class="row">
                                <!-- Invoice -->
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Facture <span class="text-danger">*</span>
                                        </label>
                                        <select name="invoice_id" class="form-select @error('invoice_id') is-invalid @enderror" required>
                                            <option value="">Sélectionner une facture</option>
                                            @foreach($invoices as $invoice)
                                                <option value="{{ $invoice->id }}" {{ old('invoice_id') == $invoice->id ? 'selected' : '' }}>
                                                    {{ $invoice->invoice_number }} - {{ $invoice->client?->first_name ?? 'Client' }} {{ $invoice->client?->last_name ?? '' }} ({{ $invoice->formatted_total_ttc }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('invoice_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Description <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="description" value="{{ old('description') }}" 
                                               class="form-control @error('description') is-invalid @enderror" 
                                               maxlength="255" required placeholder="Description de l'item...">
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Days Count -->
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Nombre de jours</label>
                                        <input type="number" name="days_count" id="days_count" value="{{ old('days_count') }}" 
                                               class="form-control @error('days_count') is-invalid @enderror" 
                                               min="1" step="1" placeholder="Nb jours">
                                        @error('days_count')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Quantity -->
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Quantité <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" name="quantity" id="quantity" value="{{ old('quantity', 1) }}" 
                                               class="form-control @error('quantity') is-invalid @enderror" 
                                               step="0.01" min="0.01" required>
                                        @error('quantity')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Unit Price TTC -->
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Prix unitaire TTC</label>
                                        <div class="input-group">
                                            <input type="number" name="unit_price_ttc" id="unit_price_ttc" value="{{ old('unit_price_ttc') }}" 
                                                   class="form-control @error('unit_price_ttc') is-invalid @enderror" 
                                                   step="0.01" min="0">
                                            <span class="input-group-text" id="currency">MAD</span>
                                        </div>
                                        @error('unit_price_ttc')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- VAT Rate -->
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">TVA (%)</label>
                                        <div class="input-group">
                                            <input type="number" name="vat_rate" id="vat_rate" value="{{ old('vat_rate', 20) }}" 
                                                   class="form-control @error('vat_rate') is-invalid @enderror" 
                                                   step="0.01" min="0" max="100">
                                            <span class="input-group-text">%</span>
                                        </div>
                                        @error('vat_rate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Hidden totals -->
                                <input type="hidden" name="total_ttc" id="total_ttc" value="0">
                                <input type="hidden" name="total_ht" id="total_ht" value="0">

                                <!-- Preview -->
                                <div class="col-md-12 mt-3">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="mb-3">Aperçu</h6>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <small class="text-muted d-block">Total HT</small>
                                                    <strong id="preview_ht">0,00 MAD</strong>
                                                </div>
                                                <div class="col-md-4">
                                                    <small class="text-muted d-block">TVA</small>
                                                    <strong id="preview_vat">0,00 MAD</strong>
                                                </div>
                                                <div class="col-md-4">
                                                    <small class="text-muted d-block">Total TTC</small>
                                                    <strong id="preview_ttc">0,00 MAD</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-3 mt-4">
                                <a href="{{ route('backoffice.invoice-items.index') }}" class="btn btn-light px-4">
                                    <i class="ti ti-x me-1"></i> Annuler
                                </a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="ti ti-device-floppy me-1"></i> Créer l'item
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const quantity = document.getElementById('quantity');
    const unitPrice = document.getElementById('unit_price_ttc');
    const vatRate = document.getElementById('vat_rate');
    const totalTtc = document.getElementById('total_ttc');
    const totalHt = document.getElementById('total_ht');
    const previewHt = document.getElementById('preview_ht');
    const previewVat = document.getElementById('preview_vat');
    const previewTtc = document.getElementById('preview_ttc');
    const currency = document.getElementById('currency').textContent.trim();

    function calculate() {
        const qty = parseFloat(quantity.value) || 0;
        const price = parseFloat(unitPrice.value) || 0;
        const vat = parseFloat(vatRate.value) || 0;
        
        // Calculate TTC (Quantity * Unit Price)
        const ttc = qty * price;
        
        // Calculate HT from TTC using VAT rate
        // If VAT is 20%, then TTC = HT * 1.2, so HT = TTC / 1.2
        const vatMultiplier = 1 + (vat / 100);
        const ht = vat > 0 ? ttc / vatMultiplier : ttc;
        const vatAmount = ttc - ht;
        
        // Update hidden fields with 2 decimal places
        totalTtc.value = ttc.toFixed(2);
        totalHt.value = ht.toFixed(2);
        
        // Update preview with formatted numbers
        previewTtc.textContent = ttc.toFixed(2).replace('.', ',') + ' ' + currency;
        previewHt.textContent = ht.toFixed(2).replace('.', ',') + ' ' + currency;
        previewVat.textContent = vatAmount.toFixed(2).replace('.', ',') + ' ' + currency;
    }

    // Initial calculation
    calculate();

    // Add event listeners
    if (quantity && unitPrice && vatRate) {
        [quantity, unitPrice, vatRate].forEach(input => {
            input.addEventListener('input', calculate);
        });
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