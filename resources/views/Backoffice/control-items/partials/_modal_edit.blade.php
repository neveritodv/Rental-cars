<?php $page = 'control-items'; ?>
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
                    <a href="{{ route('backoffice.control-items.index') }}" class="d-inline-flex align-items-center fw-medium">
                        <i class="ti ti-arrow-left me-1"></i> Retour à la liste
                    </a>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="ti ti-edit me-2"></i>
                            Modifier l'élément
                        </h4>
                        <p class="text-muted mb-0 mt-1">
                            {{ $item->item_key }}
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
                                    <i class="ti ti-checkbox"></i>
                                    Statut & Commentaire
                                </a>
                            </div>
                        </div>

                        <form action="{{ route('backoffice.control-items.update', $item) }}" method="POST" class="needs-validation" novalidate>
                            @csrf
                            @method('PUT')

                            <!-- Tab 1: Informations -->
                            <fieldset class="fieldset active" id="tab1">
                                <div class="row">
                                    <!-- Control -->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Contrôle <span class="text-danger">*</span>
                                            </label>
                                            <select name="vehicle_control_id" class="form-select @error('vehicle_control_id') is-invalid @enderror" required>
                                                <option value="">Sélectionner un contrôle</option>
                                                @foreach($controls ?? [] as $control)
                                                    <option value="{{ $control->id }}" {{ old('vehicle_control_id', $item->vehicle_control_id) == $control->id ? 'selected' : '' }}>
                                                        {{ $control->control_number }} - {{ $control->vehicle->registration_number ?? 'N/A' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('vehicle_control_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Item Key (Read Only) -->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Clé</label>
                                            <input type="text" value="{{ $item->item_key }}" 
                                                   class="form-control bg-light" readonly disabled>
                                            <small class="text-muted">La clé ne peut pas être modifiée</small>
                                        </div>
                                    </div>

                                    <!-- Label -->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Libellé</label>
                                            <input type="text" name="label" value="{{ old('label', $item->label) }}" 
                                                   class="form-control @error('label') is-invalid @enderror" 
                                                   maxlength="150">
                                            @error('label')
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

                            <!-- Tab 2: Statut & Commentaire -->
                            <fieldset class="fieldset" id="tab2">
                                <div class="row">
                                    <!-- Status -->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Statut <span class="text-danger">*</span>
                                            </label>
                                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                                <option value="">Sélectionner un statut</option>
                                                <option value="yes" {{ old('status', $item->status) == 'yes' ? 'selected' : '' }}>Oui (Conforme)</option>
                                                <option value="no" {{ old('status', $item->status) == 'no' ? 'selected' : '' }}>Non (Non conforme)</option>
                                                <option value="na" {{ old('status', $item->status) == 'na' ? 'selected' : '' }}>N/A (Non applicable)</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Comment -->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Commentaire</label>
                                            <textarea name="comment" rows="4" 
                                                      class="form-control @error('comment') is-invalid @enderror" 
                                                      placeholder="Ajoutez un commentaire sur cet élément...">{{ old('comment', $item->comment) }}</textarea>
                                            @error('comment')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-light prev-tab" data-prev="1">
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