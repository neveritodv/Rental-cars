<?php $page = 'bookings'; ?>
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
            <div class="col-lg-10">

                <div class="mb-3">
                    <a href="{{ route('backoffice.bookings.index') }}" class="d-inline-flex align-items-center fw-medium">
                        <i class="ti ti-arrow-left me-1"></i> Retour à la liste
                    </a>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="ti ti-edit me-2"></i>
                            Modifier la réservation #{{ $booking->id }}
                        </h4>
                    </div>

                    <div class="card-body">
                        <!-- Wizard Navigation -->
                        <div class="wizard-nav">
                            <div class="nav-item">
                                <a class="nav-link active" data-tab="1">
                                    <i class="ti ti-user"></i>
                                    Client & Véhicule
                                </a>
                            </div>
                            <div class="nav-item">
                                <a class="nav-link" data-tab="2">
                                    <i class="ti ti-calendar"></i>
                                    Dates & Lieux
                                </a>
                            </div>
                            <div class="nav-item">
                                <a class="nav-link" data-tab="3">
                                    <i class="ti ti-currency-dollar"></i>
                                    Informations
                                </a>
                            </div>
                        </div>

                        <form action="{{ route('backoffice.bookings.update', $booking) }}" method="POST" class="needs-validation" novalidate>
                            @csrf
                            @method('PUT')

                            <!-- Tab 1: Client & Véhicule -->
                            <fieldset class="fieldset active" id="tab1">
                                <div class="row">
                                    <!-- Client -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Client</label>
                                            <select name="client_id" class="form-select @error('client_id') is-invalid @enderror">
                                                <option value="">Sélectionner un client (optionnel)</option>
                                                @foreach($clients as $client)
                                                    <option value="{{ $client->id }}" {{ old('client_id', $booking->client_id) == $client->id ? 'selected' : '' }}>
                                                        {{ $client->first_name }} {{ $client->last_name }} - {{ $client->phone }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('client_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Vehicle -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Véhicule</label>
                                            <select name="vehicle_id" class="form-select @error('vehicle_id') is-invalid @enderror" id="vehicleSelect">
                                                <option value="">Sélectionner un véhicule (optionnel)</option>
                                                @foreach($vehicles as $vehicle)
                                                    <option value="{{ $vehicle->id }}" 
                                                            data-rate="{{ $vehicle->daily_rate }}"
                                                            {{ old('vehicle_id', $booking->vehicle_id) == $vehicle->id ? 'selected' : '' }}>
                                                        {{ $vehicle->registration_number }} - {{ $vehicle->model->name ?? 'N/C' }}
                                                        @if($vehicle->daily_rate) ({{ number_format($vehicle->daily_rate, 2) }} MAD/jour) @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('vehicle_id')
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

                            <!-- Tab 2: Dates & Lieux -->
                            <fieldset class="fieldset" id="tab2">
                                <div class="row">
                                    <!-- Start Date -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Date de début <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" name="start_date" id="startDate" 
                                                   value="{{ old('start_date', $booking->start_date->format('Y-m-d')) }}" 
                                                   class="form-control @error('start_date') is-invalid @enderror" required>
                                            @error('start_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- End Date -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Date de fin <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" name="end_date" id="endDate" 
                                                   value="{{ old('end_date', $booking->end_date->format('Y-m-d')) }}" 
                                                   class="form-control @error('end_date') is-invalid @enderror" required>
                                            @error('end_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Pickup Location -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Lieu de prise en charge <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="pickup_location" value="{{ old('pickup_location', $booking->pickup_location) }}" 
                                                   class="form-control @error('pickup_location') is-invalid @enderror" required>
                                            @error('pickup_location')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Dropoff Location -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Lieu de restitution <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="dropoff_location" value="{{ old('dropoff_location', $booking->dropoff_location) }}" 
                                                   class="form-control @error('dropoff_location') is-invalid @enderror" required>
                                            @error('dropoff_location')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Duration (calculated) -->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <div class="alert alert-info">
                                                <i class="ti ti-info-circle me-1"></i>
                                                <span id="durationDisplay">Nombre de jours: <strong>{{ $booking->booked_days }}</strong> jour(s)</span>
                                            </div>
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

                            <!-- Tab 3: Informations -->
                            <fieldset class="fieldset" id="tab3">
                                <div class="row">
                                    <!-- Estimated Total -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Montant estimé (MAD)</label>
                                            <div class="input-group">
                                                <input type="number" name="estimated_total" id="estimatedTotal" 
                                                       value="{{ old('estimated_total', $booking->estimated_total) }}" 
                                                       class="form-control @error('estimated_total') is-invalid @enderror" 
                                                       step="0.01" min="0">
                                                <span class="input-group-text">MAD</span>
                                            </div>
                                            @error('estimated_total')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Source -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Source <span class="text-danger">*</span>
                                            </label>
                                            <select name="source" class="form-select @error('source') is-invalid @enderror" required>
                                                <option value="">Sélectionner une source</option>
                                                <option value="website" {{ old('source', $booking->source) == 'website' ? 'selected' : '' }}>Site web</option>
                                                <option value="mobile" {{ old('source', $booking->source) == 'mobile' ? 'selected' : '' }}>Application mobile</option>
                                                <option value="backoffice" {{ old('source', $booking->source) == 'backoffice' ? 'selected' : '' }}>Backoffice</option>
                                                <option value="other" {{ old('source', $booking->source) == 'other' ? 'selected' : '' }}>Autre</option>
                                            </select>
                                            @error('source')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Status -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Statut <span class="text-danger">*</span>
                                            </label>
                                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                                <option value="">Sélectionner un statut</option>
                                                <option value="pending" {{ old('status', $booking->status) == 'pending' ? 'selected' : '' }}>En attente</option>
                                                <option value="confirmed" {{ old('status', $booking->status) == 'confirmed' ? 'selected' : '' }}>Confirmé</option>
                                                <option value="cancelled" {{ old('status', $booking->status) == 'cancelled' ? 'selected' : '' }}>Annulé</option>
                                                <option value="converted" {{ old('status', $booking->status) == 'converted' ? 'selected' : '' }}>Converti</option>
                                            </select>
                                            @error('status')
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

    // Date calculation
    const startDate = document.getElementById('startDate');
    const endDate = document.getElementById('endDate');
    const durationDisplay = document.getElementById('durationDisplay');
    const vehicleSelect = document.getElementById('vehicleSelect');
    const estimatedTotal = document.getElementById('estimatedTotal');

    function calculateDays() {
        if (startDate.value && endDate.value) {
            const start = new Date(startDate.value);
            const end = new Date(endDate.value);
            const diffTime = end - start;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            if (diffDays > 0) {
                durationDisplay.innerHTML = `<i class="ti ti-info-circle me-1"></i>Nombre de jours: <strong>${diffDays}</strong> jour(s)`;
                
                // Calculate estimated total if vehicle selected and estimated total is empty
                if (vehicleSelect.value && vehicleSelect.selectedOptions[0].dataset.rate && !estimatedTotal.value) {
                    const rate = parseFloat(vehicleSelect.selectedOptions[0].dataset.rate);
                    estimatedTotal.value = (rate * diffDays).toFixed(2);
                }
            } else {
                durationDisplay.innerHTML = '<i class="ti ti-alert-circle me-1 text-danger"></i>La date de fin doit être postérieure à la date de début';
            }
        }
    }

    if (startDate) startDate.addEventListener('change', calculateDays);
    if (endDate) endDate.addEventListener('change', calculateDays);
    if (vehicleSelect) {
        vehicleSelect.addEventListener('change', function() {
            if (startDate.value && endDate.value && !estimatedTotal.value) {
                calculateDays();
            }
        });
    }

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