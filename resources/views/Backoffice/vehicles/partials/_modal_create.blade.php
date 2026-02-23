<?php $page = 'add-car'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content me-0">
        <div class="mb-3">
            <a href="{{ route('backoffice.vehicles.index') }}" class="d-inline-flex align-items-center fw-medium">
                <i class="ti ti-arrow-left me-1"></i>Retour à la liste
            </a>
        </div>

        {{-- DISPLAY SESSION ERRORS --}}
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="ti ti-alert-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- DISPLAY VALIDATION ERRORS --}}
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="ti ti-alert-circle me-2"></i>
                <strong>Erreur de validation:</strong>
                <ul class="mb-0 mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card mb-0">
            <div class="card-body">
                <div class="add-wizard car-steps">

                    {{-- WIZARD NAV --}}
                    <ul class="nav d-flex align-items-center flex-wrap gap-3">
                        <li class="nav-item active">
                            <a href="javascript:void(0);" class="nav-link d-flex align-items-center">
                                <i class="ti ti-info-circle me-1"></i>Infos de base
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="javascript:void(0);" class="nav-link d-flex align-items-center">
                                <i class="ti ti-flame me-1"></i>Options
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="javascript:void(0);" class="nav-link d-flex align-items-center">
                                <i class="ti ti-files me-1"></i>Tarifs
                            </a>
                        </li>
                    </ul>

                    {{-- =========================
                    STEP 1 : BASIC
                    ========================= --}}
                    <fieldset id="first-field">
                        <form class="needs-validation" novalidate>
                            <div class="filterbox p-20 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <h4 class="d-flex align-items-center">
                                    <i class="ti ti-info-circle text-secondary me-2"></i>Informations de base
                                </h4>
                            </div>

                            {{-- PHOTO --}}
                            <div class="border-bottom mb-4 pb-4">
                                <div class="row row-gap-4">
                                    <div class="col-xl-3">
                                        <h6 class="mb-1">Photos</h6>
                                        <p>Ajouter des photos du véhicule</p>
                                    </div>
                                    <div class="col-xl-9">
                                        <div class="d-flex align-items-center flex-wrap row-gap-3 upload-pic">
                                            <div class="d-flex align-items-center justify-content-center avatar avatar-xxl me-3 flex-shrink-0 border rounded-circle frames">
                                                <img id="previewVehiclePhoto" src="{{ asset('assets/place-holder.webp') }}" class="img-fluid rounded-circle" alt="photo">
                                                <a href="javascript:void(0);" class="upload-img-trash trash-end btn btn-sm rounded-circle" id="clearVehiclePhoto">
                                                    <i class="ti ti-trash fs-12"></i>
                                                </a>
                                            </div>
                                            <div>
                                                <div class="drag-upload-btn btn btn-md btn-dark d-inline-flex align-items-center mb-2">
                                                    <i class="ti ti-photo me-1"></i>Choisir
                                                    <input type="file" class="form-control image-sign" name="photos[]" id="vehiclePhotosInput" accept="image/*" multiple form="vehicleMainForm">
                                                </div>
                                                <p class="mb-0">Taille recommandée : 500px x 500px</p>
                                                @error('photos')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                                @error('photos.*')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- CAR INFO --}}
                            <div class="border-bottom mb-2 pb-2">
                                <div class="row row-gap-4">
                                    <div class="col-xl-3">
                                        <h6 class="mb-1">Véhicule</h6>
                                        <p>Renseigner les informations principales</p>
                                    </div>

                                    <div class="col-xl-9">
                                        <div class="row">
                                            {{-- MODELE --}}
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Modèle <span class="text-danger">*</span></label>
                                                    <select class="select" name="vehicle_model_id" required form="vehicleMainForm">
                                                        <option value="">Sélectionner</option>
                                                        @foreach ($models as $model)
                                                            <option value="{{ $model->id }}" @selected(old('vehicle_model_id') == $model->id)>
                                                                {{ $model->name }} @if ($model->brand) - {{ $model->brand->name }} @endif
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div class="invalid-feedback">Veuillez choisir un modèle.</div>
                                                    @error('vehicle_model_id')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- PLATE WITH DUPLICATE CHECK --}}
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Immatriculation <span class="text-danger">*</span></label>
                                                    <div class="position-relative">
                                                        <input type="text" 
                                                               class="form-control @error('registration_number') is-invalid @enderror" 
                                                               name="registration_number" 
                                                               id="registration_number"
                                                               value="{{ old('registration_number') }}" 
                                                               required 
                                                               form="vehicleMainForm"
                                                               autocomplete="off"
                                                               placeholder="Ex: 1234 A 123">
                                                        <div id="plate-status" class="mt-1 small"></div>
                                                    </div>
                                                    <div class="invalid-feedback">Veuillez saisir l’immatriculation.</div>
                                                    @error('registration_number')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- CITY --}}
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Ville d’immatriculation</label>
                                                    <input type="text" class="form-control" name="registration_city" form="vehicleMainForm" value="{{ old('registration_city') }}" placeholder="Ex: Casablanca">
                                                    @error('registration_city')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- VIN --}}
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">VIN</label>
                                                    <input type="text" class="form-control" name="vin" value="{{ old('vin') }}" form="vehicleMainForm" placeholder="Numéro de série">
                                                    @error('vin')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- COLOR --}}
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Couleur <span class="text-danger">*</span></label>
                                                    <select class="select2-color" name="color" required form="vehicleMainForm">
                                                        <option value="">Sélectionner</option>
                                                        <option value="red" @selected(old('color') === 'red')>Rouge</option>
                                                        <option value="green" @selected(old('color') === 'green')>Vert</option>
                                                        <option value="blue" @selected(old('color') === 'blue')>Bleu</option>
                                                        <option value="black" @selected(old('color') === 'black')>Noir</option>
                                                        <option value="white" @selected(old('color') === 'white')>Blanc</option>
                                                        <option value="gray" @selected(old('color') === 'gray')>Gris</option>
                                                        <option value="silver" @selected(old('color') === 'silver')>Argent</option>
                                                        <option value="yellow" @selected(old('color') === 'yellow')>Jaune</option>
                                                    </select>
                                                    <div class="invalid-feedback">Veuillez choisir une couleur.</div>
                                                    @error('color')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- YEAR --}}
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Année <span class="text-danger">*</span></label>
                                                    <div class="input-icon-end position-relative">
                                                        <input type="text" class="form-control yearpicker" name="year" value="{{ old('year') }}" required form="vehicleMainForm" placeholder="2024">
                                                        <span class="input-icon-addon"><i class="ti ti-calendar"></i></span>
                                                    </div>
                                                    <div class="invalid-feedback">Veuillez saisir l’année.</div>
                                                    @error('year')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- CURRENT MILEAGE --}}
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Kilométrage actuel</label>
                                                    <input type="number" class="form-control" name="current_mileage" value="{{ old('current_mileage') }}" min="0" form="vehicleMainForm" placeholder="0">
                                                    @error('current_mileage')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- STATUS --}}
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Statut <span class="text-danger">*</span></label>
                                                    <select class="select" name="status" required form="vehicleMainForm">
                                                        <option value="">Sélectionner</option>
                                                        <option value="available" @selected(old('status') === 'available')>Disponible</option>
                                                        <option value="unavailable" @selected(old('status') === 'unavailable')>Indisponible</option>
                                                        <option value="maintenance" @selected(old('status') === 'maintenance')>Maintenance</option>
                                                        <option value="sold" @selected(old('status') === 'sold')>Vendu</option>
                                                    </select>
                                                    <div class="invalid-feedback">Veuillez choisir un statut.</div>
                                                    @error('status')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- NOTES --}}
                                            <div class="col-lg-8 col-md-12">
                                                <div class="mb-3">
                                                    <label class="form-label">Notes</label>
                                                    <textarea class="form-control" name="notes" rows="3" form="vehicleMainForm" placeholder="Informations complémentaires...">{{ old('notes') }}</textarea>
                                                    @error('notes')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-end pt-3">
                                <a href="{{ route('backoffice.vehicles.index') }}" class="btn btn-light d-flex align-items-center me-2">
                                    <i class="ti ti-chevron-left me-1"></i>Annuler
                                </a>
                                <button class="btn btn-primary wizard-next d-flex align-items-center" type="button" id="next-to-options">
                                    Options <i class="ti ti-chevron-right ms-1"></i>
                                </button>
                            </div>
                        </form>
                    </fieldset>

                    {{-- =========================
                    STEP 2 : FEATURES (7 EQUIPMENTS)
                    ========================= --}}
                    <fieldset>
                        <form class="needs-validation" novalidate>
                            <div class="filterbox p-20 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <h4 class="d-flex align-items-center">
                                    <i class="ti ti-flame text-secondary me-2"></i>Options & Équipements
                                </h4>
                            </div>

                            <div class="border-bottom mb-2 pb-2">
                                <div class="row row-gap-4">
                                    <div class="col-xl-3">
                                        <h6 class="mb-1">Équipements</h6>
                                        <p>Configurer les options disponibles (7 équipements)</p>
                                    </div>

                                    <div class="col-xl-9">
                                        <div class="row">
                                            {{-- Equipment 1: GPS --}}
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="has_gps" name="has_gps" value="1" form="vehicleMainForm" @checked(old('has_gps'))>
                                                        <label class="form-check-label" for="has_gps">
                                                            <i class="ti ti-map-pin me-1 text-primary"></i>
                                                            GPS
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Equipment 2: Climatisation --}}
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="has_air_conditioning" name="has_air_conditioning" value="1" form="vehicleMainForm" checked>
                                                        <label class="form-check-label" for="has_air_conditioning">
                                                            <i class="ti ti-snowflake me-1 text-info"></i>
                                                            Climatisation
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Equipment 3: Bluetooth --}}
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="has_bluetooth" name="has_bluetooth" value="1" form="vehicleMainForm" @checked(old('has_bluetooth'))>
                                                        <label class="form-check-label" for="has_bluetooth">
                                                            <i class="ti ti-bluetooth me-1 text-primary"></i>
                                                            Bluetooth
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Equipment 4: Baby Seat (formerly USB) --}}
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="has_baby_seat" name="has_baby_seat" value="1" form="vehicleMainForm" @checked(old('has_baby_seat'))>
                                                        <label class="form-check-label" for="has_baby_seat">
                                                            <i class="ti ti-baby-carriage me-1 text-success"></i>
                                                            Siège bébé
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Equipment 5: Caméra de recul --}}
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="has_camera_recul" name="has_camera_recul" value="1" form="vehicleMainForm" @checked(old('has_camera_recul'))>
                                                        <label class="form-check-label" for="has_camera_recul">
                                                            <i class="ti ti-camera me-1 text-warning"></i>
                                                            Caméra de recul
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Equipment 6: Régulateur de vitesse --}}
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="has_regulateur_vitesse" name="has_regulateur_vitesse" value="1" form="vehicleMainForm" @checked(old('has_regulateur_vitesse'))>
                                                        <label class="form-check-label" for="has_regulateur_vitesse">
                                                            <i class="ti ti-speedometer me-1 text-danger"></i>
                                                            Régulateur de vitesse
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Equipment 7: Sièges chauffants --}}
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="has_siege_chauffant" name="has_siege_chauffant" value="1" form="vehicleMainForm" @checked(old('has_siege_chauffant'))>
                                                        <label class="form-check-label" for="has_siege_chauffant">
                                                            <i class="ti ti-heat me-1 text-warning"></i>
                                                            Sièges chauffants
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="border-bottom mb-2 pb-2">
                                <div class="row row-gap-4">
                                    <div class="col-xl-3">
                                        <h6 class="mb-1">Carburant</h6>
                                        <p>Configurer la politique carburant</p>
                                    </div>

                                    <div class="col-xl-9">
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Politique carburant</label>
                                                    <select class="select" name="fuel_policy" form="vehicleMainForm">
                                                        <option value="">Sélectionner</option>
                                                        <option value="full_to_full" @selected(old('fuel_policy') === 'full_to_full')>Plein à plein</option>
                                                        <option value="same_to_same" @selected(old('fuel_policy') === 'same_to_same')>Même niveau</option>
                                                    </select>
                                                    @error('fuel_policy')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-lg-3 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Carburant (sortie)</label>
                                                    <input type="number" step="0.01" min="0" max="1" class="form-control" name="fuel_level_out" value="{{ old('fuel_level_out') }}" placeholder="0.00 - 1.00" form="vehicleMainForm">
                                                    @error('fuel_level_out')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-lg-3 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Carburant (retour)</label>
                                                    <input type="number" step="0.01" min="0" max="1" class="form-control" name="fuel_level_in" value="{{ old('fuel_level_in') }}" placeholder="0.00 - 1.00" form="vehicleMainForm">
                                                    @error('fuel_level_in')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-end pt-3">
                                <button type="button" class="btn btn-outline-light border wizard-prev me-2">
                                    <i class="ti ti-chevron-left me-1"></i>Retour
                                </button>
                                <button class="btn btn-primary wizard-next d-flex align-items-center" type="button">
                                    Tarifs <i class="ti ti-chevron-right ms-1"></i>
                                </button>
                            </div>
                        </form>
                    </fieldset>

                    {{-- =========================
                    STEP 3 : PRICING
                    ========================= --}}
                    <fieldset>
                        <form class="needs-validation" novalidate>
                            <div class="filterbox p-20 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <h4 class="d-flex align-items-center">
                                    <i class="ti ti-files text-secondary me-2"></i>Tarifs
                                </h4>
                            </div>

                            <div class="border-bottom mb-2 pb-2">
                                <div class="row row-gap-4">
                                    <div class="col-xl-3">
                                        <h6 class="mb-1">Prix</h6>
                                        <p>Configurer les montants</p>
                                    </div>

                                    <div class="col-xl-9">
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Prix / jour (MAD) <span class="text-danger">*</span></label>
                                                    <input type="number" step="0.01" min="0" class="form-control @error('daily_rate') is-invalid @enderror" name="daily_rate" value="{{ old('daily_rate') }}" required form="vehicleMainForm" placeholder="0.00">
                                                    <div class="invalid-feedback">Veuillez saisir le prix/jour.</div>
                                                    @error('daily_rate')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-lg-6 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Caution (MAD)</label>
                                                    <input type="number" step="0.01" min="0" class="form-control" name="deposit_amount" value="{{ old('deposit_amount') }}" form="vehicleMainForm" placeholder="0.00">
                                                    @error('deposit_amount')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-end pt-3">
                                <button type="button" class="btn btn-outline-light border wizard-prev me-2">
                                    <i class="ti ti-chevron-left me-1"></i>Retour
                                </button>
                                <button class="btn btn-primary d-flex align-items-center" type="submit" form="vehicleMainForm" id="submitBtn">
                                    Enregistrer <i class="ti ti-chevron-right ms-1"></i>
                                </button>
                            </div>
                        </form>
                    </fieldset>

                    {{-- HIDDEN MAIN FORM --}}
                    <form id="vehicleMainForm" method="POST" action="{{ route('backoffice.vehicles.store') }}" enctype="multipart/form-data" class="d-none">
                        @csrf
                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- Footer-->
    <div class="footer d-sm-flex align-items-center justify-content-between bg-white p-3">
        <p class="mb-0">
            <a href="javascript:void(0);">Politique de confidentialité</a>
            <a href="javascript:void(0);" class="ms-4">Conditions d’utilisation</a>
        </p>
        <p>&copy; 2025 Dreamsrent, Made with <span class="text-danger">❤</span> by <a href="javascript:void(0);" class="text-secondary">Dreams</a></p>
    </div>
</div>

{{-- TOAST CONTAINER --}}
<div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

{{-- JS: preview + bootstrap validation + checkbox sync + duplicate check + toast --}}
<script>
// Toast notification function
function showToast(title, message, type = 'info') {
    const toastColors = {
        success: '#198754',
        danger: '#dc3545',
        warning: '#ffc107',
        info: '#0dcaf0'
    };
    
    const toastContainer = document.getElementById('toast-container');
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = 'toast show';
    toast.style.cssText = 'min-width: 350px; margin-bottom: 10px; background: white; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);';
    toast.innerHTML = `
        <div class="toast-header" style="border-bottom: 1px solid #dee2e6; padding: 12px 16px; border-radius: 8px 8px 0 0;">
            <strong class="me-auto" style="color: ${toastColors[type] || toastColors.info};">${title}</strong>
            <button type="button" class="btn-close" onclick="this.closest('.toast').remove()"></button>
        </div>
        <div class="toast-body" style="padding: 12px 16px; color: #212529;">
            ${message}
        </div>
    `;
    
    toastContainer.appendChild(toast);
    
    // Auto remove after 5 seconds
    setTimeout(() => toast.remove(), 5000);
}

// Global AJAX error handler
window.addEventListener('unhandledrejection', function(event) {
    if (event.reason && event.reason.status === 422) {
        showToast('Erreur de validation', 'Veuillez vérifier les informations saisies.', 'warning');
    } else if (event.reason && event.reason.status === 500) {
        showToast('Erreur serveur', 'Une erreur est survenue. Veuillez réessayer.', 'danger');
    }
});

document.addEventListener('DOMContentLoaded', function() {
    // ===== Bootstrap validation
    document.querySelectorAll('.needs-validation').forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // ===== Photos preview
    const input = document.getElementById('vehiclePhotosInput');
    const preview = document.getElementById('previewVehiclePhoto');
    const clearBtn = document.getElementById('clearVehiclePhoto');

    if (input && preview) {
        input.addEventListener('change', function() {
            const file = input.files && input.files[0] ? input.files[0] : null;
            if (!file) return;
            const url = URL.createObjectURL(file);
            preview.src = url;
        });
    }

    if (clearBtn && input && preview) {
        clearBtn.addEventListener('click', function() {
            input.value = '';
            preview.src = "{{ asset('assets/place-holder.webp') }}";
        });
    }

    // ===== DUPLICATE PLATE CHECK - AJAX
    const plateInput = document.getElementById('registration_number');
    const plateStatus = document.getElementById('plate-status');
    const submitBtn = document.getElementById('submitBtn');
    const nextBtn = document.getElementById('next-to-options');
    let checkTimeout;

    if (plateInput) {
        plateInput.addEventListener('input', function() {
            clearTimeout(checkTimeout);
            const plate = this.value.trim();
            
            if (plate.length < 3) {
                if (plateStatus) plateStatus.innerHTML = '';
                plateInput.classList.remove('is-invalid', 'is-valid');
                if (submitBtn) submitBtn.disabled = false;
                if (nextBtn) nextBtn.disabled = false;
                return;
            }

            // Show loading state
            if (plateStatus) {
                plateStatus.innerHTML = '<span class="text-muted"><i class="ti ti-loader me-1"></i>Vérification...</span>';
            }

            checkTimeout = setTimeout(() => {
                fetch('{{ route("backoffice.vehicles.check-duplicate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ registration_number: plate })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.exists) {
                        plateInput.classList.add('is-invalid');
                        plateInput.classList.remove('is-valid');
                        if (plateStatus) {
                            plateStatus.innerHTML = '<span class="text-danger"><i class="ti ti-alert-circle me-1"></i>' + data.message + '</span>';
                        }
                        if (submitBtn) {
                            submitBtn.disabled = true;
                            submitBtn.title = 'Veuillez changer le numéro d\'immatriculation';
                        }
                        if (nextBtn) {
                            nextBtn.disabled = true;
                            nextBtn.title = 'Veuillez changer le numéro d\'immatriculation';
                        }
                    } else {
                        plateInput.classList.remove('is-invalid');
                        plateInput.classList.add('is-valid');
                        if (plateStatus) {
                            plateStatus.innerHTML = '<span class="text-success"><i class="ti ti-check me-1"></i>' + data.message + '</span>';
                        }
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.title = '';
                        }
                        if (nextBtn) {
                            nextBtn.disabled = false;
                            nextBtn.title = '';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (plateStatus) {
                        plateStatus.innerHTML = '<span class="text-warning"><i class="ti ti-alert-triangle me-1"></i>Erreur de vérification</span>';
                    }
                });
            }, 500);
        });
    }

    // Initial check if old value exists
    if (plateInput && plateInput.value.trim() !== '') {
        plateInput.dispatchEvent(new Event('input'));
    }

    // ===== Prevent form submit if duplicate exists
    const mainForm = document.getElementById('vehicleMainForm');
    if (mainForm) {
        mainForm.addEventListener('submit', function(e) {
            const plateInput = document.getElementById('registration_number');
            const isInvalid = plateInput?.classList.contains('is-invalid');
            
            if (isInvalid) {
                e.preventDefault();
                showToast('Erreur de validation', 'Veuillez corriger les erreurs avant de soumettre.', 'danger');
                return false;
            }
        });
    }
});
</script>
@endsection