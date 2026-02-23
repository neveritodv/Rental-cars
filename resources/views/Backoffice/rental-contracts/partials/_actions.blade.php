<div class="dropdown">
    <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-haspopup="true">
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
        
        <!-- PDF Export Options -->
        <li>
            <a class="dropdown-item rounded-1" href="{{ route('backoffice.contracts.pdf.single', $contract->id) }}" target="_blank">
                <i class="ti ti-file-text me-2" style="color: #dc3545;"></i>Exporter PDF (détail)
            </a>
        </li>
        
        <li>
            <form method="POST" action="{{ route('backoffice.contracts.pdf.multiple') }}" style="display: block;">
                @csrf
                <input type="hidden" name="ids[]" value="{{ $contract->id }}">
                <button type="submit" class="dropdown-item rounded-1 text-primary" style="background: none; border: none; width: 100%; text-align: left;">
                    <i class="ti ti-file-export me-2"></i>Exporter PDF (liste)
                </button>
            </form>
        </li>
        
        <li>
            <hr class="dropdown-divider">
        </li>
        <li>
            <a class="dropdown-item rounded-1 text-danger" 
               href="javascript:void(0);"
               data-bs-toggle="modal" 
               data-bs-target="#delete_contract"
               data-delete-action="{{ route('backoffice.rental-contracts.destroy', $contract) }}"
               data-delete-details="Contrat <strong>{{ $contract->contract_number }}</strong>">
                <i class="ti ti-trash me-2"></i>Supprimer
            </a>
        </li>
    </ul>
</div>