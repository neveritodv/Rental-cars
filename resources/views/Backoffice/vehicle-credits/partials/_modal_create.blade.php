<?php $page = 'vehicle-credits'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<div class="page-wrapper">
    <div class="content me-0">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="mb-3">
                    <a href="{{ route('backoffice.vehicle-credits.index') }}" class="d-inline-flex align-items-center fw-medium">
                        <i class="ti ti-arrow-left me-1"></i>
                        Retour à la liste
                    </a>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="ti ti-credit-card me-2"></i>
                            Ajouter un crédit
                        </h4>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('backoffice.vehicle-credits.store') }}" 
                              method="POST" 
                              class="needs-validation" 
                              novalidate
                              enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <!-- Vehicle Selection -->
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Véhicule <span class="text-danger">*</span>
                                        </label>
                                        <select name="vehicle_id" 
                                                id="vehicle_select"
                                                class="form-select @error('vehicle_id') is-invalid @enderror" 
                                                required>
                                            <option value="">Sélectionner un véhicule</option>
                                            @foreach($vehicles as $v)
                                                <option value="{{ $v->id }}" 
                                                    {{ old('vehicle_id', $vehicle->id ?? '') == $v->id ? 'selected' : '' }}>
                                                    {{ $v->registration_number }} - {{ $v->brand->name ?? '' }} {{ $v->model->name ?? '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('vehicle_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Creditor Name -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Créancier <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                               name="creditor_name"
                                               value="{{ old('creditor_name') }}"
                                               class="form-control @error('creditor_name') is-invalid @enderror"
                                               maxlength="150"
                                               placeholder="Ex: Banque, Institution...">
                                        @error('creditor_name')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Total Amount -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Montant total <span class="text-danger">*</span> (MAD)
                                        </label>
                                        <div class="input-group">
                                            <input type="number"
                                                   name="total_amount"
                                                   value="{{ old('total_amount') }}"
                                                   class="form-control @error('total_amount') is-invalid @enderror"
                                                   required
                                                   step="0.01"
                                                   min="0"
                                                   placeholder="0.00">
                                            <span class="input-group-text">DH</span>
                                        </div>
                                        @error('total_amount')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Down Payment -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Apport (MAD)
                                        </label>
                                        <div class="input-group">
                                            <input type="number"
                                                   name="down_payment"
                                                   value="{{ old('down_payment', 0) }}"
                                                   class="form-control @error('down_payment') is-invalid @enderror"
                                                   step="0.01"
                                                   min="0"
                                                   placeholder="0.00">
                                            <span class="input-group-text">DH</span>
                                        </div>
                                        @error('down_payment')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Monthly Payment -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Mensualité <span class="text-danger">*</span> (MAD)
                                        </label>
                                        <div class="input-group">
                                            <input type="number"
                                                   name="monthly_payment"
                                                   value="{{ old('monthly_payment') }}"
                                                   class="form-control @error('monthly_payment') is-invalid @enderror"
                                                   required
                                                   step="0.01"
                                                   min="0"
                                                   placeholder="0.00">
                                            <span class="input-group-text">DH</span>
                                        </div>
                                        @error('monthly_payment')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Duration -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Durée <span class="text-danger">*</span> (mois)
                                        </label>
                                        <input type="number"
                                               name="duration_months"
                                               value="{{ old('duration_months') }}"
                                               class="form-control @error('duration_months') is-invalid @enderror"
                                               required
                                               min="1"
                                               max="120"
                                               placeholder="Ex: 36">
                                        @error('duration_months')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Interest Rate -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Taux d'intérêt (%)
                                        </label>
                                        <div class="input-group">
                                            <input type="number"
                                                   name="interest_rate"
                                                   value="{{ old('interest_rate', 0) }}"
                                                   class="form-control @error('interest_rate') is-invalid @enderror"
                                                   step="0.01"
                                                   min="0"
                                                   max="100"
                                                   placeholder="0.00">
                                            <span class="input-group-text">%</span>
                                        </div>
                                        @error('interest_rate')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Start Date -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Date de début <span class="text-danger">*</span>
                                        </label>
                                        <input type="date"
                                               name="start_date"
                                               value="{{ old('start_date', date('Y-m-d')) }}"
                                               class="form-control @error('start_date') is-invalid @enderror"
                                               required>
                                        @error('start_date')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Contract File -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Contrat (PDF)
                                        </label>
                                        <input type="file"
                                               name="contract_file"
                                               class="form-control @error('contract_file') is-invalid @enderror"
                                               accept=".pdf,.jpg,.jpeg,.png"
                                               max="2048">
                                        <small class="text-muted">Max 2MB, formats: PDF, JPG, PNG</small>
                                        @error('contract_file')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Notes -->
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Notes</label>
                                        <textarea name="notes"
                                                  class="form-control @error('notes') is-invalid @enderror"
                                                  rows="4"
                                                  placeholder="Informations complémentaires...">{{ old('notes') }}</textarea>
                                        @error('notes')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-3 mt-4">
                                <a href="{{ route('backoffice.vehicle-credits.index') }}" class="btn btn-light px-4">
                                    <i class="ti ti-x me-1"></i>
                                    Annuler
                                </a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="ti ti-device-floppy me-1"></i>
                                    Créer le crédit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
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

    // Initialize select2 if available
    if (typeof $.fn.select2 !== 'undefined' && document.getElementById('vehicle_select')) {
        $('#vehicle_select').select2({
            placeholder: 'Sélectionner un véhicule',
            allowClear: true,
            width: '100%'
        });
    }

    // Calculate remaining amount
    const totalInput = document.querySelector('input[name="total_amount"]');
    const downInput = document.querySelector('input[name="down_payment"]');
    
    if (totalInput && downInput) {
        const calculateRemaining = () => {
            const total = parseFloat(totalInput.value) || 0;
            const down = parseFloat(downInput.value) || 0;
            const remaining = total - down;
            console.log('Montant restant:', remaining);
        };
        
        totalInput.addEventListener('input', calculateRemaining);
        downInput.addEventListener('input', calculateRemaining);
    }
});
</script>
@endpush
@endsection