@props(['contract'])

<div class="dropdown">
    <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-haspopup="true">
        <i class="ti ti-dots-vertical"></i>
    </button>

    <ul class="dropdown-menu dropdown-menu-end p-2">
        {{-- Voir détails - contrôlé par permission VIEW --}}
        @can('rental-contracts.general.view')
        <li>
            <a class="dropdown-item rounded-1" href="{{ route('backoffice.rental-contracts.show', $contract) }}">
                <i class="ti ti-eye me-2"></i>Voir détails
            </a>
        </li>
        @endcan

        {{-- Modifier - contrôlé par permission EDIT --}}
        @can('rental-contracts.general.edit')
        <li>
            <a class="dropdown-item rounded-1" href="{{ route('backoffice.rental-contracts.edit', $contract) }}">
                <i class="ti ti-edit me-2"></i>Modifier
            </a>
        </li>
        @endcan
        
        <!-- PDF Export Options - contrôlé par permission VIEW -->
        @can('rental-contracts.general.view')
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
        @endcan

        <!-- WhatsApp - contrôlé par permission VIEW -->
        @can('rental-contracts.general.view')
            @if($contract->primaryClient && $contract->primaryClient->phone)
            <li>
                <a class="dropdown-item rounded-1 text-success" 
                   href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contract->primaryClient->phone) }}?text={{ urlencode('Bonjour, voici votre contrat #' . $contract->contract_number . ' : ' . route('backoffice.contracts.pdf.single', $contract->id, true)) }}"
                   target="_blank">
                    <i class="ti ti-brand-whatsapp me-2"></i>Envoyer vers whatsapp
                </a>
            </li>
            @else
            <li>
                <a class="dropdown-item rounded-1 text-muted" 
                   href="javascript:void(0);"
                   onclick="alert('Ce client n\'a pas de numéro de téléphone')">
                    <i class="ti ti-brand-whatsapp me-2"></i>Pas de numéro
                </a>
            </li>
            @endif
        @endcan

        {{-- Supprimer - contrôlé par permission DELETE --}}
        @can('rental-contracts.general.delete')
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
        @endcan
    </ul>
</div>