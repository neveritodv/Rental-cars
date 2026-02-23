<?php $page = 'edit-car'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<div class="page-wrapper">
    <div class="content me-4 pb-0">

        <div class="mb-3">
            <a href="{{ route('backoffice.vehicles.index') }}" class="d-inline-flex align-items-center fw-medium">
                <i class="ti ti-arrow-left me-1"></i>Retour à la liste
            </a>
        </div>

        <div class="card mb-0">
            <div class="card-body">
                <div class="add-wizard car-steps">

                    {{-- Steps (UI only) --}}
                    <ul class="nav d-flex align-items-center flex-wrap gap-3">
                        <li class="nav-item active">
                            <a href="javascript:void(0);" class="nav-link d-flex align-items-center">
                                <i class="ti ti-info-circle me-1"></i>Informations
                            </a>
                        </li>
                    </ul>

                    <form action="{{ route('backoffice.vehicles.update', $vehicle) }}" method="POST"
                        enctype="multipart/form-data" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')

                        <fieldset id="first-field">
                            <div
                                class="filterbox p-20 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <h4 class="d-flex align-items-center">
                                    <i class="ti ti-info-circle text-secondary me-2"></i>Infos de base
                                </h4>
                            </div>

                            {{-- Featured image --}}
                            <div class="border-bottom mb-4 pb-4">
                                <div class="row row-gap-4">
                                    <div class="col-xl-3">
                                        <h6 class="mb-1">Image principale</h6>
                                        <p>Télécharger/Changer l'image</p>
                                    </div>
                                    <div class="col-xl-9">
                                        <div class="d-flex align-items-center flex-wrap row-gap-3 upload-pic">
                                            <div
                                                class="d-flex align-items-center justify-content-center avatar avatar-xxl me-3 flex-shrink-0 border rounded-circle frames">
                                                <img src="{{ $vehicle->getMainPhotoUrlAttribute() }}"
                                                    class="img-fluid rounded-circle" alt="vehicle" style="width: 100px; height: 100px; object-fit: cover;">
                                            </div>
                                            <div>
                                                <div
                                                    class="drag-upload-btn btn btn-md btn-dark d-inline-flex align-items-center mb-2">
                                                    <i class="ti ti-photo me-1"></i>Changer
                                                    <input type="file" name="photos[]"
                                                        class="form-control image-sign" accept="image/*" multiple>
                                                </div>
                                                <p>Taille recommandée : 500px x 500px</p>
                                                @error('photos')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Car info --}}
                            <div class="border-bottom mb-2 pb-2">
                                <div class="row row-gap-4">
                                    <div class="col-xl-3">
                                        <h6 class="mb-1">Informations véhicule</h6>
                                        <p>Modifier les informations</p>
                                    </div>

                                    <div class="col-xl-9">
                                        {{-- Registration number (required) --}}
                                        <div class="mb-3">
                                            <label class="form-label">Numéro d'immatriculation <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="registration_number"
                                                class="form-control @error('registration_number') is-invalid @enderror"
                                                value="{{ old('registration_number', $vehicle->registration_number) }}"
                                                required>
                                            @error('registration_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- VIN --}}
                                        <div class="mb-3">
                                            <label class="form-label">Numéro VIN</label>
                                            <input type="text" name="vin"
                                                class="form-control @error('vin') is-invalid @enderror"
                                                value="{{ old('vin', $vehicle->vin) }}">
                                            @error('vin')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="row">
                                            {{-- Brand --}}
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Marque</label>
                                                    <input type="text" class="form-control"
                                                        value="{{ optional($vehicle->model?->brand)->name ?? 'N/A' }}" readonly>
                                                </div>
                                            </div>

                                            {{-- Model (required) --}}
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Modèle <span
                                                            class="text-danger">*</span></label>
                                                    <select name="vehicle_model_id"
                                                        class="select @error('vehicle_model_id') is-invalid @enderror"
                                                        required>
                                                        <option value="">Sélectionner</option>
                                                        @foreach ($models ?? [] as $model)
                                                        <option value="{{ $model->id }}"
                                                            @selected(old('vehicle_model_id', $vehicle->vehicle_model_id) == $model->id)>
                                                            {{ $model->name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                    @error('vehicle_model_id')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- Year (required) --}}
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Année <span
                                                            class="text-danger">*</span></label>
                                                    <div class="input-icon-end position-relative">
                                                        <input type="number" name="year" min="1900"
                                                            max="{{ date('Y') + 1 }}"
                                                            class="form-control @error('year') is-invalid @enderror"
                                                            value="{{ old('year', $vehicle->year) }}" required>
                                                        <span class="input-icon-addon">
                                                            <i class="ti ti-calendar"></i>
                                                        </span>
                                                    </div>
                                                    @error('year')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- Color (required) --}}
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Couleur <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" name="color"
                                                        class="form-control @error('color') is-invalid @enderror"
                                                        value="{{ old('color', $vehicle->color) }}" required>
                                                    @error('color')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- Current mileage --}}
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Kilométrage actuel</label>
                                                    <input type="number" name="current_mileage" min="0"
                                                        class="form-control @error('current_mileage') is-invalid @enderror"
                                                        value="{{ old('current_mileage', $vehicle->current_mileage) }}">
                                                    @error('current_mileage')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- Daily rate (required) --}}
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Tarif journalier (MAD) <span
                                                            class="text-danger">*</span></label>
                                                    <input type="number" name="daily_rate" step="0.01" min="0"
                                                        class="form-control @error('daily_rate') is-invalid @enderror"
                                                        value="{{ old('daily_rate', $vehicle->daily_rate) }}" required>
                                                    @error('daily_rate')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- Status (required) --}}
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Statut <span
                                                            class="text-danger">*</span></label>
                                                    <select name="status"
                                                        class="select @error('status') is-invalid @enderror" required>
                                                        <option value="">Sélectionner</option>
                                                        <option value="available" @selected(old('status', $vehicle->status) === 'available')>Disponible</option>
                                                        <option value="unavailable" @selected(old('status', $vehicle->status) === 'unavailable')>Indisponible</option>
                                                        <option value="maintenance" @selected(old('status', $vehicle->status) === 'maintenance')>En maintenance</option>
                                                        <option value="sold" @selected(old('status', $vehicle->status) === 'sold')>Vendu</option>
                                                        <option value="booked" @selected(old('status', $vehicle->status) === 'booked')>Réservé</option>
                                                    </select>
                                                    @error('status')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- Fuel policy (required) --}}
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Politique carburant <span
                                                            class="text-danger">*</span></label>
                                                    <select name="fuel_policy"
                                                        class="select @error('fuel_policy') is-invalid @enderror"
                                                        required>
                                                        <option value="">Sélectionner</option>
                                                        <option value="full_to_full" @selected(old('fuel_policy', $vehicle->fuel_policy) === 'full_to_full')>Plein à plein</option>
                                                        <option value="same_to_same" @selected(old('fuel_policy', $vehicle->fuel_policy) === 'same_to_same')>Même niveau</option>
                                                    </select>
                                                    @error('fuel_policy')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- Registration city --}}
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Ville d'immatriculation</label>
                                                    <input type="text" name="registration_city"
                                                        class="form-control @error('registration_city') is-invalid @enderror"
                                                        value="{{ old('registration_city', $vehicle->registration_city) }}">
                                                    @error('registration_city')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- Deposit amount --}}
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Montant de la caution (MAD)</label>
                                                    <input type="number" name="deposit_amount" step="0.01" min="0"
                                                        class="form-control @error('deposit_amount') is-invalid @enderror"
                                                        value="{{ old('deposit_amount', $vehicle->deposit_amount) }}">
                                                    @error('deposit_amount')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- All 7 Equipment Options --}}
                                            <div class="col-lg-12">
                                                <div class="mb-3">
                                                    <h6 class="mb-2">Équipements</h6>
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <div class="form-check form-switch">
                                                                <input type="checkbox" name="has_gps" id="has_gps" value="1"
                                                                    class="form-check-input" @checked(old('has_gps', $vehicle->has_gps))>
                                                                <label class="form-check-label" for="has_gps">
                                                                    <i class="ti ti-map-pin me-1 text-primary"></i>GPS
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-check form-switch">
                                                                <input type="checkbox" name="has_air_conditioning" id="has_air_conditioning" value="1"
                                                                    class="form-check-input" @checked(old('has_air_conditioning', $vehicle->has_air_conditioning))>
                                                                <label class="form-check-label" for="has_air_conditioning">
                                                                    <i class="ti ti-snowflake me-1 text-info"></i>Climatisation
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-check form-switch">
                                                                <input type="checkbox" name="has_bluetooth" id="has_bluetooth" value="1"
                                                                    class="form-check-input" @checked(old('has_bluetooth', $vehicle->has_bluetooth))>
                                                                <label class="form-check-label" for="has_bluetooth">
                                                                    <i class="ti ti-bluetooth me-1 text-primary"></i>Bluetooth
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-check form-switch">
                                                                <input type="checkbox" name="has_baby_seat" id="has_baby_seat" value="1"
                                                                    class="form-check-input" @checked(old('has_baby_seat', $vehicle->has_baby_seat))>
                                                                <label class="form-check-label" for="has_baby_seat">
                                                                    <i class="ti ti-baby-carriage me-1 text-success"></i>Siège bébé
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-check form-switch">
                                                                <input type="checkbox" name="has_camera_recul" id="has_camera_recul" value="1"
                                                                    class="form-check-input" @checked(old('has_camera_recul', $vehicle->has_camera_recul))>
                                                                <label class="form-check-label" for="has_camera_recul">
                                                                    <i class="ti ti-camera me-1 text-warning"></i>Caméra de recul
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-check form-switch">
                                                                <input type="checkbox" name="has_regulateur_vitesse" id="has_regulateur_vitesse" value="1"
                                                                    class="form-check-input" @checked(old('has_regulateur_vitesse', $vehicle->has_regulateur_vitesse))>
                                                                <label class="form-check-label" for="has_regulateur_vitesse">
                                                                    <i class="ti ti-speedometer me-1 text-danger"></i>Régulateur de vitesse
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-check form-switch">
                                                                <input type="checkbox" name="has_siege_chauffant" id="has_siege_chauffant" value="1"
                                                                    class="form-check-input" @checked(old('has_siege_chauffant', $vehicle->has_siege_chauffant))>
                                                                <label class="form-check-label" for="has_siege_chauffant">
                                                                    <i class="ti ti-heat me-1 text-warning"></i>Sièges chauffants
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Notes --}}
                                            <div class="col-lg-12">
                                                <div class="mb-3">
                                                    <label class="form-label">Notes</label>
                                                    <textarea name="notes"
                                                        class="form-control @error('notes') is-invalid @enderror"
                                                        rows="3">{{ old('notes', $vehicle->notes) }}</textarea>
                                                    @error('notes')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="d-flex align-items-center justify-content-end pt-3">
                                <a href="{{ route('backoffice.vehicles.index') }}"
                                    class="btn btn-light d-flex align-items-center me-2">
                                    <i class="ti ti-chevron-left me-1"></i>Annuler
                                </a>
                                <button class="btn btn-primary d-flex align-items-center" type="submit">
                                    Enregistrer
                                    <i class="ti ti-device-floppy ms-1"></i>
                                </button>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="footer d-sm-flex align-items-center justify-content-between bg-white p-3">
        <p class="mb-0">
            <a href="javascript:void(0);">Politique de confidentialité</a>
            <a href="javascript:void(0);" class="ms-4">Conditions d'utilisation</a>
        </p>
        <p>&copy; 2025 Dreamsrent</p>
    </div>
</div>
@endsection