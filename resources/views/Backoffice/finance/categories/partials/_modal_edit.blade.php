<?php $page = 'finance-categories'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<div class="page-wrapper">
    <div class="content me-0">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="mb-3">
                    <a href="{{ route('backoffice.finance.categories.index') }}" class="d-inline-flex align-items-center fw-medium">
                        <i class="ti ti-arrow-left me-1"></i> Retour à la liste
                    </a>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="ti ti-edit me-2"></i>
                            Modifier la catégorie
                        </h4>
                        <p class="text-muted mb-0 mt-1">
                            {{ $transactionCategory->name }}
                        </p>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('backoffice.finance.categories.update', $transactionCategory) }}" method="POST" class="needs-validation" novalidate>
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <!-- Name -->
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Nom de la catégorie <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="name" value="{{ old('name', $transactionCategory->name) }}" 
                                               class="form-control @error('name') is-invalid @enderror" 
                                               required maxlength="150">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Type -->
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Type <span class="text-danger">*</span>
                                        </label>
                                        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                            <option value="">Sélectionner un type</option>
                                            <option value="income" {{ old('type', $transactionCategory->type) == 'income' ? 'selected' : '' }}>Revenu</option>
                                            <option value="expense" {{ old('type', $transactionCategory->type) == 'expense' ? 'selected' : '' }}>Dépense</option>
                                            <option value="both" {{ old('type', $transactionCategory->type) == 'both' ? 'selected' : '' }}>Les deux</option>
                                        </select>
                                        @error('type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-3 mt-4">
                                <a href="{{ route('backoffice.finance.categories.index') }}" class="btn btn-light px-4">
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
@endsection