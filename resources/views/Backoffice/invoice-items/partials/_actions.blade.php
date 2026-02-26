@props(['item'])

<div class="dropdown">
    <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-haspopup="true">
        <i class="ti ti-dots-vertical"></i>
    </button>

    <ul class="dropdown-menu dropdown-menu-end p-2">
        {{-- Voir détails - contrôlé par permission VIEW --}}
        @can('invoice-items.general.view')
        <li>
            <a class="dropdown-item rounded-1" href="{{ route('backoffice.invoice-items.show', $item) }}">
                <i class="ti ti-eye me-2"></i>Voir détails
            </a>
        </li>
        @endcan

        {{-- Modifier - contrôlé par permission EDIT --}}
        @can('invoice-items.general.edit')
        <li>
            <a class="dropdown-item rounded-1" href="{{ route('backoffice.invoice-items.edit', $item) }}">
                <i class="ti ti-edit me-2"></i>Modifier
            </a>
        </li>
        @endcan

        {{-- Supprimer - contrôlé par permission DELETE --}}
        @can('invoice-items.general.delete')
        <li>
            <hr class="dropdown-divider">
        </li>
        <li>
            <a class="dropdown-item rounded-1 text-danger" 
               href="javascript:void(0);"
               data-bs-toggle="modal" 
               data-bs-target="#delete_invoice_item"
               data-delete-action="{{ route('backoffice.invoice-items.destroy', $item) }}"
               data-delete-details="Item <strong>{{ $item->description }}</strong>">
                <i class="ti ti-trash me-2"></i>Supprimer
            </a>
        </li>
        @endcan
    </ul>
</div>