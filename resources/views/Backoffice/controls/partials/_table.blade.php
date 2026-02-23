<div class="table-responsive">
    <table class="table align-middle">
        <thead class="thead-light">
            <tr>
                <th width="50" class="text-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="select-all">
                    </div>
                </th>
                <th>N° Contrôle</th>
                <th>Véhicule</th>
                <th>Contrat</th>
                <th>Distance</th>
                <th>Statut</th>
                <th>Date</th>
                <th width="80">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($controls as $control)
            <tr>
                <td class="text-center">
                    <div class="form-check">
                        <input class="form-check-input control-checkbox" type="checkbox" value="{{ $control->id }}">
                    </div>
                </td>
                <td>
                    <a href="{{ route('backoffice.controls.show', $control) }}" class="fw-medium text-dark">
                        {{ $control->control_number }}
                    </a>
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
                        <span class="fw-medium text-dark">{{ $vehicleRegNumber }}</span>
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
                        <span class="text-dark">
                            #{{ $contractNumber }}
                        </span>
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
                <td class="text-center">
                    <div class="dropdown">
                        <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-haspopup="true">
                            <i class="ti ti-dots-vertical"></i>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end p-2">
                            <li>
                                <a class="dropdown-item rounded-1" href="{{ route('backoffice.controls.show', $control) }}">
                                    <i class="ti ti-eye me-2"></i>Voir détails
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item rounded-1" href="{{ route('backoffice.controls.edit', $control) }}">
                                    <i class="ti ti-edit me-2"></i>Modifier
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item rounded-1 text-danger" 
                                   href="javascript:void(0);"
                                   data-bs-toggle="modal" 
                                   data-bs-target="#deleteControlModal"
                                   data-delete-action="{{ route('backoffice.controls.destroy', $control) }}"
                                   data-delete-details="le contrôle <strong>{{ $control->control_number }}</strong>">
                                    <i class="ti ti-trash me-2"></i>Supprimer
                                </a>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center py-5">
                    <div class="text-center">
                        <i class="ti ti-clipboard-list fs-48 text-gray-4 mb-3"></i>
                        <h5 class="mb-2">Aucun contrôle trouvé</h5>
                        <p class="text-muted mb-3">Commencez par créer un nouveau contrôle véhicule</p>
                        <a href="{{ route('backoffice.controls.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-2"></i>Nouveau contrôle
                        </a>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

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

        // Initialize dropdowns
        initializeAllDropdowns();
        
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.dropdown')) {
                closeAllDropdowns();
            }
        });
    });

    function initializeAllDropdowns() {
        document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(button => {
            button.removeEventListener('click', dropdownClickHandler);
            button.addEventListener('click', dropdownClickHandler);
        });
    }

    function dropdownClickHandler(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const button = this;
        const dropdown = button.nextElementSibling;
        const isExpanded = button.getAttribute('aria-expanded') === 'true';
        
        closeAllDropdowns();
        
        if (dropdown && !isExpanded) {
            dropdown.classList.add('show');
            button.setAttribute('aria-expanded', 'true');
        }
    }

    function closeAllDropdowns() {
        document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
            menu.classList.remove('show');
            const toggle = menu.previousElementSibling;
            if (toggle && toggle.hasAttribute('data-bs-toggle="dropdown"')) {
                toggle.setAttribute('aria-expanded', 'false');
            }
        });
    }
</script>