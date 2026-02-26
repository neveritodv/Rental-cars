<style>
    .btn-icon {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        color: #6c757d;
        background: transparent;
        border: 1px solid transparent;
        transition: all 0.2s;
        cursor: pointer;
    }
    
    .btn-icon:hover {
        background: #f8f9fa;
        border-color: #dee2e6;
        color: #0d6efd;
    }
    
    .btn-icon i {
        font-size: 18px;
    }
    
    .dropdown-menu {
        z-index: 9999 !important;
        display: block;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s;
    }
    
    .dropdown-menu.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    
    .badge-completed { 
        background: #d4edda; 
        color: #155724; 
        padding: 0.35rem 0.75rem; 
        border-radius: 50px; 
        font-weight: 500; 
    }
    
    .badge-pending { 
        background: #fff3cd; 
        color: #856404; 
        padding: 0.35rem 0.75rem; 
        border-radius: 50px; 
        font-weight: 500; 
    }
    
    .form-check {
        display: flex;
        justify-content: center;
        margin: 0;
        padding: 0;
    }
    
    .text-dark {
        color: #212529 !important;
    }
    
    .fw-medium {
        font-weight: 500;
    }
</style>

<div class="table-responsive">
    <table class="table align-middle">
        <thead class="thead-light">
            <tr>
                {{-- Case à cocher - visible seulement si permission DELETE --}}
                @can('vehicle-controls.general.delete')
                <th width="50" class="text-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="select-all">
                    </div>
                </th>
                @endcan
                <th>N° Contrôle</th>
                <th>Véhicule</th>
                <th>Contrat</th>
                <th>Distance</th>
                <th>Statut</th>
                <th>Date</th>
                {{-- Colonne Actions - visible seulement si au moins une permission d'action --}}
                @canany(['vehicle-controls.general.view', 'vehicle-controls.general.edit', 'vehicle-controls.general.delete'])
                <th width="80">Actions</th>
                @endcanany
            </tr>
        </thead>
        <tbody>
            @forelse($controls as $control)
            <tr>
                {{-- Case à cocher - visible seulement si permission DELETE --}}
                @can('vehicle-controls.general.delete')
                <td class="text-center">
                    <div class="form-check">
                        <input class="form-check-input control-checkbox" type="checkbox" value="{{ $control->id }}">
                    </div>
                </td>
                @endcan
                
                <td>
                    {{-- Lien vers show - contrôlé par permission VIEW --}}
                    @can('vehicle-controls.general.view')
                        <a href="{{ route('backoffice.controls.show', $control) }}" class="fw-medium text-dark">
                            {{ $control->control_number }}
                        </a>
                    @else
                        <span class="fw-medium text-dark">{{ $control->control_number }}</span>
                    @endcan
                </td>
                
                <td>
                    @php
                        $vehicleRegNumber = 'N/A';
                        $vehicleDetails = '';
                        
                        if ($control->vehicle) {
                            $vehicleRegNumber = $control->vehicle->registration_number ?? 'N/A';
                            
                            // Get brand and model through relationships
                            if ($control->vehicle->relationLoaded('model') && $control->vehicle->model) {
                                $brandName = $control->vehicle->model->brand->name ?? '';
                                $modelName = $control->vehicle->model->name ?? '';
                                if ($brandName || $modelName) {
                                    $vehicleDetails = trim($brandName . ' ' . $modelName);
                                }
                            }
                        }
                    @endphp
                    <div class="d-flex flex-column">
                        {{-- Lien vers véhicule - contrôlé par permission VIEW sur véhicules --}}
                        @can('vehicles.general.view')
                            <a href="{{ $control->vehicle ? route('backoffice.vehicles.show', $control->vehicle) : '#' }}" class="fw-medium text-dark">
                                {{ $vehicleRegNumber }}
                            </a>
                        @else
                            <span class="fw-medium text-dark">{{ $vehicleRegNumber }}</span>
                        @endcan
                        @if($vehicleDetails)
                            <small class="text-muted">{{ $vehicleDetails }}</small>
                        @endif
                    </div>
                </td>
                
                <td>
                    @if($control->rentalContract)
                        @php
                            $contractNumber = $control->rentalContract->contract_number ?? 'N°' . $control->rentalContract->id;
                        @endphp
                        {{-- Lien vers contrat - contrôlé par permission VIEW sur contrats --}}
                        @can('rental-contracts.general.view')
                            <a href="{{ route('backoffice.rental-contracts.show', $control->rentalContract) }}" class="text-dark">
                                #{{ $contractNumber }}
                            </a>
                        @else
                            <span class="text-dark">#{{ $contractNumber }}</span>
                        @endcan
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                
                <td>
                    @if($control->total_distance)
                        <span class="text-dark fw-medium">
                            {{ number_format($control->total_distance, 0, ',', ' ') }} KM
                        </span>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                
                <td>
                    <span class="badge {{ $control->status_badge_class }}">
                        {{ $control->status_text }}
                    </span>
                </td>
                
                <td>
                    <div class="d-flex flex-column">
                        <span class="text-dark">{{ $control->created_at->format('d/m/Y') }}</span>
                        <small class="text-muted">{{ $control->created_at->format('H:i') }}</small>
                    </div>
                </td>
                
                {{-- Actions - visible seulement si au moins une permission d'action --}}
                @canany(['vehicle-controls.general.view', 'vehicle-controls.general.edit', 'vehicle-controls.general.delete'])
                <td class="text-center">
                    @include('Backoffice.controls.partials._actions', ['control' => $control])
                </td>
                @endcanany
            </tr>
            @empty
            <tr>
                @can('vehicle-controls.general.delete')
                <td></td>
                @endcan
                <td colspan="{{ (auth()->user()->can('vehicle-controls.general.delete') ? 7 : 6) }}" class="text-center py-5">
                    <div class="text-center">
                        <i class="ti ti-clipboard-list fs-48 text-gray-4 mb-3"></i>
                        <h5 class="mb-2">Aucun contrôle trouvé</h5>
                        @can('vehicle-controls.general.create')
                            <p class="text-muted mb-3">Commencez par créer un nouveau contrôle véhicule</p>
                            <a href="{{ route('backoffice.controls.create') }}" class="btn btn-primary">
                                <i class="ti ti-plus me-2"></i>Nouveau contrôle
                            </a>
                        @endcan
                    </div>
                </td>
                @canany(['vehicle-controls.general.view', 'vehicle-controls.general.edit', 'vehicle-controls.general.delete'])
                <td></td>
                @endcanany
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Script pour "Select All" - seulement si permission DELETE --}}
@can('vehicle-controls.general.delete')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                document.querySelectorAll('.control-checkbox').forEach(cb => {
                    cb.checked = selectAll.checked;
                });
            });
        }
    });
</script>
@endcan