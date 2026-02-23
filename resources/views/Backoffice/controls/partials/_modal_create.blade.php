<?php $page = 'vehicle-controls'; ?>
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
    .generated-number {
        background-color: #e8f5e9;
        border: 1px solid #c3e6cb;
        color: #155724;
        font-weight: 600;
        font-family: monospace;
        font-size: 1.1rem;
    }
</style>

<div class="page-wrapper">
    <div class="content me-0">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="mb-3">
                    <a href="{{ route('backoffice.controls.index') }}" class="d-inline-flex align-items-center fw-medium">
                        <i class="ti ti-arrow-left me-1"></i> Retour à la liste
                    </a>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="ti ti-clipboard-list me-2"></i>
                            Nouveau contrôle véhicule
                        </h4>
                    </div>

                    <div class="card-body">
                        <!-- Wizard Navigation -->
                        <div class="wizard-nav">
                            <div class="nav-item">
                                <a class="nav-link active" data-tab="1">
                                    <i class="ti ti-car"></i>
                                    Véhicule & Contrat
                                </a>
                            </div>
                            <div class="nav-item">
                                <a class="nav-link" data-tab="2">
                                    <i class="ti ti-speedometer"></i>
                                    Kilométrage
                                </a>
                            </div>
                            <div class="nav-item">
                                <a class="nav-link" data-tab="3">
                                    <i class="ti ti-notes"></i>
                                    Notes
                                </a>
                            </div>
                        </div>

                        <form action="{{ route('backoffice.controls.store') }}" method="POST" class="needs-validation" novalidate>
                            @csrf

                            <!-- Tab 1: Véhicule & Contrat -->
                            <fieldset class="fieldset active" id="tab1">
                                <div class="row">
                                    <!-- Vehicle -->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Véhicule <span class="text-danger">*</span>
                                            </label>
                                            <select name="vehicle_id" class="form-select @error('vehicle_id') is-invalid @enderror" required>
                                                <option value="">Sélectionner un véhicule</option>
                                                @foreach($vehicles ?? [] as $vehicle)
                                                    @php
                                                        $vehicleId = $vehicle->id;
                                                        $regNumber = $vehicle->registration_number ?? 'Sans immatriculation';
                                                        
                                                        // Get brand and model safely through relationship
                                                        $brandName = '';
                                                        $modelName = '';
                                                        
                                                        if ($vehicle->relationLoaded('model') && $vehicle->model) {
                                                            $brandName = $vehicle->model->brand->name ?? '';
                                                            $modelName = $vehicle->model->name ?? '';
                                                        } else {
                                                            // Load the relationship if not loaded
                                                            $vehicle->load('model.brand');
                                                            $brandName = $vehicle->model->brand->name ?? '';
                                                            $modelName = $vehicle->model->name ?? '';
                                                        }
                                                        
                                                        $displayText = $regNumber;
                                                        if ($brandName || $modelName) {
                                                            $displayText .= ' - ' . trim($brandName . ' ' . $modelName);
                                                        }
                                                    @endphp
                                                    <option value="{{ $vehicleId }}" {{ old('vehicle_id') == $vehicleId ? 'selected' : '' }}>
                                                        {{ $displayText }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('vehicle_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Rental Contract -->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Contrat de location <span class="text-danger">*</span>
                                            </label>
                                            <select name="rental_contract_id" class="form-select @error('rental_contract_id') is-invalid @enderror" required>
                                                <option value="">Sélectionner un contrat</option>
                                                @foreach($rentalContracts ?? [] as $contract)
                                                    @php
                                                        $contractId = $contract->id;
                                                        $contractNumber = $contract->contract_number ?? 'N°' . $contract->id;
                                                        $startDate = $contract->start_date ? \Carbon\Carbon::parse($contract->start_date)->format('d/m/Y') : '';
                                                        $displayText = '#' . $contractNumber;
                                                        if ($startDate) {
                                                            $displayText .= ' - ' . $startDate;
                                                        }
                                                    @endphp
                                                    <option value="{{ $contractId }}" {{ old('rental_contract_id') == $contractId ? 'selected' : '' }}>
                                                        {{ $displayText }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('rental_contract_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Generated Control Number (Display Only) -->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Numéro de contrôle généré</label>
                                            <div class="generated-number form-control" id="generatedControlNumber" style="background-color: #e8f5e9; border-color: #c3e6cb;">
                                                @php
                                                    // Generate a temporary control number for display
                                                    $prefix = 'CTRL';
                                                    $year = date('Y');
                                                    $month = date('m');
                                                    $random = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
                                                    $tempNumber = $prefix . '-' . $year . $month . '-' . $random;
                                                @endphp
                                                {{ $tempNumber }}
                                            </div>
                                           
                                            <input type="hidden" name="control_number" value="{{ $tempNumber }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <button type="button" class="btn btn-primary next-tab" data-next="2">
                                        Suivant <i class="ti ti-chevron-right ms-1"></i>
                                    </button>
                                </div>
                            </fieldset>

                            <!-- Tab 2: Kilométrage -->
                            <fieldset class="fieldset" id="tab2">
                                <div class="row">
                                    <!-- Start Mileage -->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Kilométrage de départ <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number" name="start_mileage" value="{{ old('start_mileage') }}" 
                                                       class="form-control @error('start_mileage') is-invalid @enderror" 
                                                       min="0" max="9999999" required>
                                                <span class="input-group-text">KM</span>
                                            </div>
                                            @error('start_mileage')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- End Mileage -->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Kilométrage d'arrivée</label>
                                            <div class="input-group">
                                                <input type="number" name="end_mileage" value="{{ old('end_mileage') }}" 
                                                       class="form-control @error('end_mileage') is-invalid @enderror" 
                                                       min="0" max="9999999" placeholder="À remplir à la fin du contrôle">
                                                <span class="input-group-text">KM</span>
                                            </div>
                                            @error('end_mileage')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Peut être laissé vide pour un contrôle en cours</small>
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

                            <!-- Tab 3: Notes -->
                            <fieldset class="fieldset" id="tab3">
                                <div class="row">
                                    <!-- Notes -->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Notes / Observations</label>
                                            <textarea name="notes" rows="6" 
                                                      class="form-control @error('notes') is-invalid @enderror" 
                                                      placeholder="Ajoutez des notes sur l'état du véhicule, observations particulières...">{{ old('notes') }}</textarea>
                                            @error('notes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Informations complémentaires sur le contrôle (max 1000 caractères)</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-light prev-tab" data-prev="2">
                                        <i class="ti ti-chevron-left me-1"></i> Précédent
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-device-floppy me-1"></i> Créer le contrôle
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

    // Validate end mileage >= start mileage
    const startMileage = document.querySelector('input[name="start_mileage"]');
    const endMileage = document.querySelector('input[name="end_mileage"]');
    
    if (startMileage && endMileage) {
        endMileage.addEventListener('input', function() {
            if (this.value && startMileage.value && parseInt(this.value) < parseInt(startMileage.value)) {
                this.setCustomValidity('Le kilométrage d\'arrivée doit être supérieur ou égal au kilométrage de départ');
            } else {
                this.setCustomValidity('');
            }
        });
    }
});
</script>
@endsection