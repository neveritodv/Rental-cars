<?php $page = 'technical-checks'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<div class="page-wrapper">
    <div class="content me-0">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="mb-3">
                    <a href="{{ route('backoffice.vehicles.technical-checks.index', $vehicle->id) }}" class="d-inline-flex align-items-center fw-medium">
                        <i class="ti ti-arrow-left me-1"></i>
                        Retour à la liste
                    </a>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="ti ti-clipboard-edit me-2"></i>
                            Modifier le contrôle technique
                        </h4>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('backoffice.vehicles.technical-checks.update', [$vehicle->id, $technicalCheck->id]) }}" 
                              method="POST" 
                              class="needs-validation" 
                              novalidate>
                            @csrf
                            @method('PUT')

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
                                                    {{ old('vehicle_id', $technicalCheck->vehicle_id) == $v->id ? 'selected' : '' }}>
                                                    {{ $v->registration_number }} - {{ $v->registration_city ?? '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('vehicle_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Date -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Date du contrôle <span class="text-danger">*</span>
                                        </label>
                                        <input type="date"
                                               name="date"
                                               value="{{ old('date', $technicalCheck->date->format('Y-m-d')) }}"
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
                                                   value="{{ old('amount', $technicalCheck->amount) }}"
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

                                <!-- Next Check Date -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Prochain contrôle <span class="text-danger">*</span>
                                        </label>
                                        <input type="date"
                                               name="next_check_date"
                                               value="{{ old('next_check_date', $technicalCheck->next_check_date->format('Y-m-d')) }}"
                                               class="form-control @error('next_check_date') is-invalid @enderror"
                                               required>
                                        @error('next_check_date')
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
                                                  rows="4">{{ old('notes', $technicalCheck->notes) }}</textarea>
                                        @error('notes')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-3 mt-4">
                                <a href="{{ route('backoffice.vehicles.technical-checks.index', $vehicle->id) }}" class="btn btn-light px-4">
                                    <i class="ti ti-x me-1"></i>
                                    Annuler
                                </a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="ti ti-device-floppy me-1"></i>
                                    Mettre à jour
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