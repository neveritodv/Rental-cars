<?php $page = 'finance-accounts'; ?>
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
                    <a href="{{ route('backoffice.finance.accounts.index') }}" class="d-inline-flex align-items-center fw-medium">
                        <i class="ti ti-arrow-left me-1"></i> Retour à la liste
                    </a>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="ti ti-edit me-2"></i>
                            Modifier le compte
                        </h4>
                        <p class="text-muted mb-0 mt-1">
                            {{ $financialAccount->name }}
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
                                    Solde
                                </a>
                            </div>
                            <div class="nav-item">
                                <a class="nav-link" data-tab="3">
                                    <i class="ti ti-settings"></i>
                                    Options
                                </a>
                            </div>
                        </div>

                        <form action="{{ route('backoffice.finance.accounts.update', $financialAccount) }}" method="POST" class="needs-validation" novalidate>
                            @csrf
                            @method('PUT')

                            <!-- Tab 1: Informations -->
                            <fieldset class="fieldset active" id="tab1">
                                <div class="row">
                                    <!-- Name -->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Nom du compte <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="name" value="{{ old('name', $financialAccount->name) }}" 
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
                                                Type de compte <span class="text-danger">*</span>
                                            </label>
                                            <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                                <option value="">Sélectionner un type</option>
                                                <option value="bank" {{ old('type', $financialAccount->type) == 'bank' ? 'selected' : '' }}>Banque</option>
                                                <option value="cash" {{ old('type', $financialAccount->type) == 'cash' ? 'selected' : '' }}>Caisse</option>
                                                <option value="other" {{ old('type', $financialAccount->type) == 'other' ? 'selected' : '' }}>Autre</option>
                                            </select>
                                            @error('type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- RIB -->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">RIB / IBAN</label>
                                            <input type="text" name="rib" value="{{ old('rib', $financialAccount->rib) }}" 
                                                   class="form-control @error('rib') is-invalid @enderror" 
                                                   maxlength="50">
                                            @error('rib')
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

                            <!-- Tab 2: Solde -->
                            <fieldset class="fieldset" id="tab2">
                                <div class="row">
                                    <!-- Initial Balance -->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Solde initial <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number" name="initial_balance" value="{{ old('initial_balance', $financialAccount->initial_balance) }}" 
                                                       class="form-control @error('initial_balance') is-invalid @enderror" 
                                                       step="0.01" min="0" required>
                                                <span class="input-group-text">MAD</span>
                                            </div>
                                            @error('initial_balance')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Solde actuel: {{ $financialAccount->formatted_current_balance }}</small>
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

                            <!-- Tab 3: Options -->
                            <fieldset class="fieldset" id="tab3">
                                <div class="row">
                                    <!-- Is Default -->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="is_default" id="is_default" value="1" {{ old('is_default', $financialAccount->is_default) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_default">
                                                    <strong>Compte par défaut</strong>
                                                </label>
                                            </div>
                                            <small class="text-muted">Si activé, ce compte sera utilisé par défaut pour les transactions</small>
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