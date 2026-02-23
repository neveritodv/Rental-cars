<?php $page = 'finance-categories'; ?>
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
                    <a href="{{ route('backoffice.finance.categories.index') }}" class="d-inline-flex align-items-center fw-medium">
                        <i class="ti ti-arrow-left me-1"></i> Retour à la liste
                    </a>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="ti ti-category me-2"></i>
                            Nouvelle catégorie
                        </h4>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('backoffice.finance.categories.store') }}" method="POST" class="needs-validation" novalidate>
                            @csrf

                            <div class="row">
                                <!-- Name -->
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Nom de la catégorie <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="name" value="{{ old('name') }}" 
                                               class="form-control @error('name') is-invalid @enderror" 
                                               required maxlength="150" placeholder="Ex: Location, Carburant, Entretien...">
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
                                            <option value="income" {{ old('type') == 'income' ? 'selected' : '' }}>Revenu</option>
                                            <option value="expense" {{ old('type') == 'expense' ? 'selected' : '' }}>Dépense</option>
                                            <option value="both" {{ old('type') == 'both' ? 'selected' : '' }}>Les deux</option>
                                        </select>
                                        @error('type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Les catégories de type "Les deux" peuvent être utilisées pour les revenus et les dépenses</small>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-3 mt-4">
                                <a href="{{ route('backoffice.finance.categories.index') }}" class="btn btn-light px-4">
                                    <i class="ti ti-x me-1"></i> Annuler
                                </a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="ti ti-device-floppy me-1"></i> Créer la catégorie
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