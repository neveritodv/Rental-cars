<div class="dropdown">
    <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-haspopup="true">
        <i class="ti ti-dots-vertical"></i>
    </button>

    <ul class="dropdown-menu dropdown-menu-end p-2">
        <li>
            <a class="dropdown-item rounded-1" href="{{ route('backoffice.finance.accounts.show', $account) }}">
                <i class="ti ti-eye me-2"></i>Voir détails
            </a>
        </li>
        <li>
            <a class="dropdown-item rounded-1" href="{{ route('backoffice.finance.accounts.edit', $account) }}">
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
               data-bs-target="#delete_account"
               data-delete-action="{{ route('backoffice.finance.accounts.destroy', $account) }}"
               data-delete-details="Compte <strong>{{ $account->name }}</strong>">
                <i class="ti ti-trash me-2"></i>Supprimer
            </a>
        </li>
    </ul>
</div>