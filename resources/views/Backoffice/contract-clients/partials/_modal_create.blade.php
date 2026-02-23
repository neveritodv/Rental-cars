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
                            <i class="ti ti-user-plus me-2"></i>
                            Ajouter une relation client-contrat
                        </h4>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('backoffice.contract-clients.store') }}" method="POST" class="needs-validation" novalidate>
                            @csrf

                            <div class="row">
                                <!-- Contract -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Contrat <span class="text-danger">*</span>
                                        </label>
                                        <select name="rental_contract_id" class="form-select @error('rental_contract_id') is-invalid @enderror" required>
                                            <option value="">Sélectionner un contrat</option>
                                            @foreach($contracts as $contract)
                                                <option value="{{ $contract->id }}" {{ old('rental_contract_id') == $contract->id ? 'selected' : '' }}>
                                                    {{ $contract->contract_number }} - {{ $contract->vehicle->registration_number ?? 'N/A' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('rental_contract_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Client -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Client <span class="text-danger">*</span>
                                        </label>
                                        <select name="client_id" class="form-select @error('client_id') is-invalid @enderror" required>
                                            <option value="">Sélectionner un client</option>
                                            @foreach($clients as $client)
                                                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                                    {{ $client->first_name }} {{ $client->last_name }} - {{ $client->phone }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('client_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
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
                                            <option value="primary" {{ old('role') == 'primary' ? 'selected' : '' }}>Principal</option>
                                            <option value="secondary" {{ old('role') == 'secondary' ? 'selected' : '' }}>Secondaire</option>
                                            <option value="other" {{ old('role') == 'other' ? 'selected' : '' }}>Autre</option>
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
                                        <input type="number" name="order" value="{{ old('order', $nextOrder) }}" 
                                               class="form-control @error('order') is-invalid @enderror" 
                                               min="1" placeholder="Ordre d'affichage">
                                        @error('order')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Laissez vide pour utiliser la valeur par défaut</small>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-3 mt-4">
                                <a href="{{ route('backoffice.contract-clients.index') }}" class="btn btn-light px-4">
                                    <i class="ti ti-x me-1"></i> Annuler
                                </a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="ti ti-device-floppy me-1"></i> Créer la relation
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