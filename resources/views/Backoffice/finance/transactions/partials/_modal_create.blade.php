<?php $page = 'finance-transactions'; ?>
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
                    <a href="{{ route('backoffice.finance.transactions.index') }}" class="d-inline-flex align-items-center fw-medium">
                        <i class="ti ti-arrow-left me-1"></i> Retour à la liste
                    </a>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="ti ti-transfer me-2"></i>
                            Nouvelle transaction
                        </h4>
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
                                    Montant
                                </a>
                            </div>
                            <div class="nav-item">
                                <a class="nav-link" data-tab="3">
                                    <i class="ti ti-details"></i>
                                    Détails
                                </a>
                            </div>
                        </div>

                        <form action="{{ route('backoffice.finance.transactions.store') }}" method="POST" class="needs-validation" novalidate>
                            @csrf

                            <!-- Tab 1: Informations -->
                            <fieldset class="fieldset active" id="tab1">
                                <div class="row">
                                    <!-- Date -->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Date <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" 
                                                   class="form-control @error('date') is-invalid @enderror" required>
                                            @error('date')
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
                                            <div class="d-flex gap-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="type" id="type_income" value="income" {{ old('type', 'income') == 'income' ? 'checked' : '' }} required>
                                                    <label class="form-check-label text-success" for="type_income">
                                                        <i class="ti ti-trending-up me-1"></i>Revenu
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="type" id="type_expense" value="expense" {{ old('type') == 'expense' ? 'checked' : '' }} required>
                                                    <label class="form-check-label text-danger" for="type_expense">
                                                        <i class="ti ti-trending-down me-1"></i>Dépense
                                                    </label>
                                                </div>
                                            </div>
                                            @error('type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Account -->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Compte <span class="text-danger">*</span>
                                            </label>
                                            <select name="financial_account_id" class="form-select @error('financial_account_id') is-invalid @enderror" required>
                                                <option value="">Sélectionner un compte</option>
                                                @foreach($accounts as $account)
                                                    <option value="{{ $account->id }}" {{ old('financial_account_id') == $account->id ? 'selected' : '' }}>
                                                        {{ $account->name }} ({{ $account->formatted_current_balance }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('financial_account_id')
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

                            <!-- Tab 2: Montant -->
                            <fieldset class="fieldset" id="tab2">
                                <div class="row">
                                    <!-- Amount -->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Montant <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number" name="amount" value="{{ old('amount') }}" 
                                                       class="form-control @error('amount') is-invalid @enderror" 
                                                       step="0.01" min="0.01" required>
                                                <span class="input-group-text">MAD</span>
                                            </div>
                                            @error('amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Category -->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Catégorie</label>
                                            <select name="transaction_category_id" class="form-select @error('transaction_category_id') is-invalid @enderror">
                                                <option value="">Sélectionner une catégorie (optionnel)</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" {{ old('transaction_category_id') == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }} ({{ $category->type_text }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('transaction_category_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
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

                            <!-- Tab 3: Détails -->
                            <fieldset class="fieldset" id="tab3">
                                <div class="row">
                                    <!-- Description -->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Description</label>
                                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                                      rows="3" placeholder="Description de la transaction...">{{ old('description') }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Reference -->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Référence</label>
                                            <input type="text" name="reference" value="{{ old('reference') }}" 
                                                   class="form-control @error('reference') is-invalid @enderror" 
                                                   maxlength="100" placeholder="Numéro de facture, reçu...">
                                            @error('reference')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-light prev-tab" data-prev="2">
                                        <i class="ti ti-chevron-left me-1"></i> Précédent
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-device-floppy me-1"></i> Créer la transaction
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