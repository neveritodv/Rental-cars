<?php $page = 'insurances'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<div class="page-wrapper">
    <div class="content me-0">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="mb-3">
                    <a href="{{ route('backoffice.vehicles.insurances.index', $vehicle->id ?? 1) }}" class="d-inline-flex align-items-center fw-medium">
                        <i class="ti ti-arrow-left me-1"></i>
                        Retour à la liste
                    </a>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="ti ti-shield-plus me-2"></i>
                            Ajouter une assurance
                        </h4>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('backoffice.vehicles.insurances.store', ['vehicle' => $vehicle->id ?? 1]) }}" 
                              method="POST" 
                              class="needs-validation" 
                              novalidate>
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
                                                    {{ $v->registration_number }} - {{ $v->registration_city ?? '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('vehicle_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Company Name -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Compagnie d'assurance
                                        </label>
                                        <input type="text"
                                               name="company_name"
                                               value="{{ old('company_name') }}"
                                               class="form-control @error('company_name') is-invalid @enderror"
                                               maxlength="150"
                                               placeholder="Ex: AXA, Wafa, Sanlam...">
                                        @error('company_name')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Policy Number -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Numéro de police
                                        </label>
                                        <input type="text"
                                               name="policy_number"
                                               value="{{ old('policy_number') }}"
                                               class="form-control @error('policy_number') is-invalid @enderror"
                                               maxlength="100"
                                               placeholder="N° police">
                                        @error('policy_number')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Date -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Date d'effet <span class="text-danger">*</span>
                                        </label>
                                        <input type="date"
                                               name="date"
                                               value="{{ old('date', date('Y-m-d')) }}"
                                               class="form-control @error('date') is-invalid @enderror"
                                               required
                                               max="{{ date('Y-m-d') }}">
                                        @error('date')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Amount -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Montant <span class="text-danger">*</span> (MAD)
                                        </label>
                                        <div class="input-group">
                                            <input type="number"
                                                   name="amount"
                                                   value="{{ old('amount') }}"
                                                   class="form-control @error('amount') is-invalid @enderror"
                                                   required
                                                   step="0.01"
                                                   min="0"
                                                   max="9999999.99"
                                                   placeholder="0.00">
                                            <span class="input-group-text">DH</span>
                                        </div>
                                        @error('amount')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Next Insurance Date -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Prochaine échéance <span class="text-danger">*</span>
                                        </label>
                                        <input type="date"
                                               name="next_insurance_date"
                                               value="{{ old('next_insurance_date', now()->addYear()->format('Y-m-d')) }}"
                                               class="form-control @error('next_insurance_date') is-invalid @enderror"
                                               required>
                                        @error('next_insurance_date')
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
                                <a href="{{ route('backoffice.vehicles.insurances.index', $vehicle->id ?? 1) }}" class="btn btn-light px-4">
                                    <i class="ti ti-x me-1"></i>
                                    Annuler
                                </a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="ti ti-device-floppy me-1"></i>
                                    Créer l'assurance
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
});
</script>
@endpush
@endsection