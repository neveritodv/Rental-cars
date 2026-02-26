@props(['technicalCheck'])

<div class="dropdown">
    <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="ti ti-dots-vertical"></i>
    </button>

    <ul class="dropdown-menu dropdown-menu-end p-2">
        {{-- Voir détails - contrôlé par permission VIEW --}}
        @can('vehicle-technical-checks.general.view')
        <li>
            <a class="dropdown-item rounded-1" href="{{ route('backoffice.vehicles.technical-checks.show', [$technicalCheck->vehicle_id, $technicalCheck->id]) }}">
                <i class="ti ti-eye me-1"></i>Voir détails
            </a>
        </li>
        @endcan

        {{-- Modifier - contrôlé par permission EDIT --}}
        @can('vehicle-technical-checks.general.edit')
        <li>
            <a class="dropdown-item rounded-1" href="{{ route('backoffice.vehicles.technical-checks.edit', [$technicalCheck->vehicle_id, $technicalCheck->id]) }}">
                <i class="ti ti-edit me-1"></i>Modifier
            </a>
        </li>
        @endcan

        {{-- Supprimer - contrôlé par permission DELETE --}}
        @can('vehicle-technical-checks.general.delete')
        <li>
            <hr class="dropdown-divider">
        </li>
        <li>
            <a class="dropdown-item rounded-1 text-danger" 
               href="javascript:void(0);"
               data-bs-toggle="modal" 
               data-bs-target="#delete_technical_check"
               data-delete-action="{{ route('backoffice.vehicles.technical-checks.destroy', [$technicalCheck->vehicle_id, $technicalCheck->id]) }}"
               data-delete-details="Contrôle du <strong>{{ $technicalCheck->formatted_date }}</strong> - {{ $technicalCheck->formatted_amount }}">
                <i class="ti ti-trash me-1"></i>Supprimer
            </a>
        </li>
        @endcan
    </ul>
</div>