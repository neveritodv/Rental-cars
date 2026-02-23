<div class="table-responsive">
    <table class="table align-middle">
        <thead class="thead-light">
            <tr>
                <th width="50" class="text-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="select-all">
                    </div>
                </th>
                <th>N° Contrat</th>
                <th>Client</th>
                <th>Véhicule</th>
                <th>Dates</th>
                <th>Montant</th>
                <th>Statut</th>
                <th width="120">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($contracts as $contract)
            <tr>
                <td class="text-center">
                    <div class="form-check">
                        <input class="form-check-input contract-checkbox" type="checkbox" value="{{ $contract->id }}">
                    </div>
                </td>
                
                <td>
                    <div class="contract-info">
                        <a href="{{ route('backoffice.rental-contracts.show', $contract) }}" class="fw-medium">
                            {{ $contract->contract_number }}
                        </a>
                        <br>
                        <small>
                            <i class="ti ti-calendar me-1"></i>{{ $contract->created_at->format('d/m/Y') }}
                        </small>
                    </div>
                </td>
                
                <td>
                    <div class="contract-info">
                        <span class="fw-medium">{{ $contract->primaryClient->first_name ?? '' }} {{ $contract->primaryClient->last_name ?? '' }}</span>
                        <br>
                        <small>
                            <i class="ti ti-phone me-1"></i>{{ $contract->primaryClient->phone ?? 'N/A' }}
                        </small>
                    </div>
                </td>
                
                <td>
                    <div class="contract-info">
                        <a href="{{ route('backoffice.vehicles.show', $contract->vehicle_id) }}" class="fw-medium">
                            {{ $contract->vehicle->registration_number ?? 'N/A' }}
                        </a>
                        <br>
                        <small>{{ $contract->vehicle->model->name ?? 'N/C' }}</small>
                    </div>
                </td>
                
                <td>
                    <div class="contract-info">
                        <span><i class="ti ti-calendar-check me-1"></i>{{ $contract->formatted_start_date }}</span>
                        <br>
                        <span><i class="ti ti-calendar-x me-1"></i>{{ $contract->formatted_end_date }}</span>
                    </div>
                </td>
                
                <td>
                    <span class="amount-badge">{{ $contract->formatted_total_amount }}</span>
                    @if($contract->deposit_amount)
                        <br><small class="text-muted">Caution: {{ $contract->formatted_deposit }}</small>
                    @endif
                </td>
                
                <td>
                    <span class="badge badge-{{ str_replace('_', '-', $contract->status) }}">
                        {{ $contract->status_text }}
                    </span>
                    <br>
                    <small>
                        <i class="ti ti-clock me-1"></i>{{ $contract->acceptance_text }}
                    </small>
                </td>
                
                <td class="text-center">
                    <div class="dropdown">
                        <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-dots-vertical"></i>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end p-2">
                            <li>
                                <a class="dropdown-item rounded-1" href="{{ route('backoffice.rental-contracts.show', $contract) }}">
                                    <i class="ti ti-eye me-2"></i>Voir détails
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item rounded-1" href="{{ route('backoffice.rental-contracts.edit', $contract) }}">
                                    <i class="ti ti-edit me-2"></i>Modifier
                                </a>
                            </li>
                            <!-- PDF Export Button -->
                            <li>
                                <a class="dropdown-item rounded-1" href="{{ route('backoffice.contracts.pdf.single', $contract->id) }}" target="_blank">
                                    <i class="ti ti-file-text me-2" style="color: #dc3545;"></i>Exporter PDF
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item rounded-1 text-danger" 
                                   href="javascript:void(0);"
                                   data-bs-toggle="modal" 
                                   data-bs-target="#deleteContractModal"
                                   data-delete-action="{{ route('backoffice.rental-contracts.destroy', $contract) }}"
                                   data-delete-details="le contrat <strong>{{ $contract->contract_number }}</strong>">
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
                        <i class="ti ti-file-text fs-48 text-gray-4 mb-3"></i>
                        <h5 class="mb-2">Aucun contrat trouvé</h5>
                        <p class="text-muted mb-3">Commencez par créer un nouveau contrat</p>
                        <a href="{{ route('backoffice.rental-contracts.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-2"></i>Nouveau contrat
                        </a>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                document.querySelectorAll('.contract-checkbox').forEach(cb => {
                    cb.checked = selectAll.checked;
                });
            });
            
            // Update select all when individual checkboxes change
            document.querySelectorAll('.contract-checkbox').forEach(cb => {
                cb.addEventListener('change', function() {
                    const allCheckboxes = document.querySelectorAll('.contract-checkbox');
                    const allChecked = Array.from(allCheckboxes).every(c => c.checked);
                    const anyChecked = Array.from(allCheckboxes).some(c => c.checked);
                    
                    selectAll.checked = allChecked;
                    selectAll.indeterminate = anyChecked && !allChecked;
                });
            });
        }
    });
</script>