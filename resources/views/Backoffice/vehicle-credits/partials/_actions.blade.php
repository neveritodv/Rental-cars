@props(['credit'])

<div class="dropdown">
    <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="ti ti-dots-vertical"></i>
    </button>

    <ul class="dropdown-menu dropdown-menu-end p-2">
        {{-- Voir détails - contrôlé par permission VIEW --}}
        @can('vehicle-credits.general.view')
        <li>
            <a class="dropdown-item rounded-1" href="{{ route('backoffice.vehicle-credits.show', $credit->id) }}">
                <i class="ti ti-eye me-1"></i>Voir détails
            </a>
        </li>
        @endcan

        {{-- Modifier - contrôlé par permission EDIT --}}
        @can('vehicle-credits.general.edit')
        <li>
            <a class="dropdown-item rounded-1" href="{{ route('backoffice.vehicle-credits.edit', $credit->id) }}">
                <i class="ti ti-edit me-1"></i>Modifier
            </a>
        </li>
        @endcan

        {{-- Échéancier - contrôlé par permission VIEW --}}
        @can('vehicle-credits.general.view')
        <li>
            <a class="dropdown-item rounded-1" href="{{ route('backoffice.vehicle-credits.payment-schedule', $credit->id) }}" target="_blank">
                <i class="ti ti-calendar-stats me-1"></i>Échéancier
            </a>
        </li>
        @endcan

        {{-- Supprimer - contrôlé par permission DELETE --}}
        @can('vehicle-credits.general.delete')
        <li>
            <hr class="dropdown-divider">
        </li>
        <li>
            <a class="dropdown-item rounded-1 text-danger" 
               href="javascript:void(0);"
               data-bs-toggle="modal" 
               data-bs-target="#delete_credit"
               data-delete-action="{{ route('backoffice.vehicle-credits.destroy', $credit->id) }}"
               data-delete-details="Crédit <strong>{{ $credit->credit_number }}</strong> - {{ $credit->creditor_name }}">
                <i class="ti ti-trash me-1"></i>Supprimer
            </a>
        </li>
        @endcan
    </ul>
</div>