<?php $page = 'contract-clients'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<div class="page-wrapper">
    <div class="content me-0">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="mb-3">
                    <a href="{{ route('backoffice.contract-clients.index') }}" class="d-inline-flex align-items-center fw-medium">
                        <i class="ti ti-arrow-left me-1"></i> Retour à la liste
                    </a>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="ti ti-edit me-2"></i>
                            Modifier la relation client-contrat
                        </h4>
                        <p class="text-muted mb-0 mt-1">
                            Relation #{{ $contractClient->id }}
                        </p>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('backoffice.contract-clients.update', $contractClient) }}" method="POST" class="needs-validation" novalidate>
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <!-- Contract (readonly) -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Contrat</label>
                                        <input type="text" class="form-control" 
                                               value="{{ $contractClient->rentalContract->contract_number ?? 'N/A' }}" readonly>
                                        <input type="hidden" name="rental_contract_id" value="{{ $contractClient->rental_contract_id }}">
                                    </div>
                                </div>

                                <!-- Client (readonly) -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Client</label>
                                        <input type="text" class="form-control" 
                                               value="{{ $contractClient->client->first_name ?? '' }} {{ $contractClient->client->last_name ?? '' }}" readonly>
                                        <input type="hidden" name="client_id" value="{{ $contractClient->client_id }}">
                                    </div>
                                </div>

                                <!-- Role -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Rôle <span class="text-danger">*</span>
                                        </label>
                                        <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                            <option value="">Sélectionner un rôle</option>
                                            <option value="primary" {{ old('role', $contractClient->role) == 'primary' ? 'selected' : '' }}>Principal</option>
                                            <option value="secondary" {{ old('role', $contractClient->role) == 'secondary' ? 'selected' : '' }}>Secondaire</option>
                                            <option value="other" {{ old('role', $contractClient->role) == 'other' ? 'selected' : '' }}>Autre</option>
                                        </select>
                                        @error('role')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Order -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Ordre</label>
                                        <input type="number" name="order" value="{{ old('order', $contractClient->order) }}" 
                                               class="form-control @error('order') is-invalid @enderror" 
                                               min="1" placeholder="Ordre d'affichage">
                                        @error('order')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-3 mt-4">
                                <a href="{{ route('backoffice.contract-clients.index') }}" class="btn btn-light px-4">
                                    <i class="ti ti-x me-1"></i> Annuler
                                </a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="ti ti-device-floppy me-1"></i> Mettre à jour
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