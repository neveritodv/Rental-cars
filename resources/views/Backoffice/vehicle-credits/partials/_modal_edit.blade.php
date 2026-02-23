<?php $page = 'vehicle-credits'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<div class="page-wrapper">
    <div class="content me-0">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="mb-3">
                    <a href="{{ route('backoffice.vehicle-credits.show', $credit->id) }}" class="d-inline-flex align-items-center fw-medium">
                        <i class="ti ti-arrow-left me-1"></i>
                        Retour aux détails
                    </a>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="ti ti-credit-card-edit me-2"></i>
                            Modifier le crédit
                        </h4>
                        <p class="text-muted mb-0 mt-1">
                            N° crédit: <strong>{{ $credit->credit_number }}</strong>
                        </p>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('backoffice.vehicle-credits.update', $credit->id) }}" 
                              method="POST" 
                              class="needs-validation" 
                              novalidate
                              enctype="multipart/form-data">
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
                                                    {{ old('vehicle_id', $credit->vehicle_id) == $v->id ? 'selected' : '' }}>
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
                                               value="{{ old('creditor_name', $credit->creditor_name) }}"
                                               class="form-control @error('creditor_name') is-invalid @enderror"
                                               maxlength="150"
                                               placeholder="Ex: Banque, Institution...">
                                        @error('creditor_name')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Statut</label>
                                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                                            <option value="active" {{ $credit->status == 'active' ? 'selected' : '' }}>Actif</option>
                                            <option value="completed" {{ $credit->status == 'completed' ? 'selected' : '' }}>Terminé</option>
                                            <option value="defaulted" {{ $credit->status == 'defaulted' ? 'selected' : '' }}>En défaut</option>
                                            <option value="pending" {{ $credit->status == 'pending' ? 'selected' : '' }}>En attente</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Contract File -->
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Contrat
                                        </label>
                                        @if($credit->contract_file)
                                            <div class="mb-2">
                                                <a href="{{ Storage::url($credit->contract_file) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="ti ti-file-text me-1"></i>
                                                    Voir le contrat actuel
                                                </a>
                                            </div>
                                        @endif
                                        <input type="file"
                                               name="contract_file"
                                               class="form-control @error('contract_file') is-invalid @enderror"
                                               accept=".pdf,.jpg,.jpeg,.png">
                                        <small class="text-muted">Laissez vide pour garder le fichier actuel</small>
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
                                                  rows="4">{{ old('notes', $credit->notes) }}</textarea>
                                        @error('notes')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <i class="ti ti-info-circle me-1"></i>
                                Pour modifier les montants ou la durée, veuillez contacter un administrateur.
                            </div>

                            <div class="d-flex justify-content-end gap-3 mt-4">
                                <a href="{{ route('backoffice.vehicle-credits.show', $credit->id) }}" class="btn btn-light px-4">
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

    // Initialize select2 if available
    if (typeof $.fn.select2 !== 'undefined' && document.getElementById('vehicle_select')) {
        $('#vehicle_select').select2({
            placeholder: 'Sélectionner un véhicule',
            allowClear: true,
            width: '100%'
        });
    }
});
</script>
@endpush
@endsection