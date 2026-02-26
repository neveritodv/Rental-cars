@props(['oilChange'])

<div class="dropdown">
    <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="ti ti-dots-vertical"></i>
    </button>

    <ul class="dropdown-menu dropdown-menu-end p-2">
        {{-- Voir détails - contrôlé par permission VIEW --}}
        @can('vehicle-oil-changes.general.view')
        <li>
            <a class="dropdown-item rounded-1" href="{{ route('backoffice.vehicles.oil-changes.show', [$oilChange->vehicle_id, $oilChange->id]) }}">
                <i class="ti ti-eye me-1"></i>Voir détails
            </a>
        </li>
        @endcan

        {{-- Modifier - contrôlé par permission EDIT --}}
        @can('vehicle-oil-changes.general.edit')
        <li>
            <a class="dropdown-item rounded-1" href="{{ route('backoffice.vehicles.oil-changes.edit', [$oilChange->vehicle_id, $oilChange->id]) }}">
                <i class="ti ti-edit me-1"></i>Modifier
            </a>
        </li>
        @endcan

        {{-- Supprimer - contrôlé par permission DELETE --}}
        @can('vehicle-oil-changes.general.delete')
        <li>
            <hr class="dropdown-divider">
        </li>
        <li>
            <a class="dropdown-item rounded-1 text-danger" 
               href="javascript:void(0);"
               data-bs-toggle="modal" 
               data-bs-target="#delete_oil_change"
               data-delete-action="{{ route('backoffice.vehicles.oil-changes.destroy', [$oilChange->vehicle_id, $oilChange->id]) }}"
               data-delete-details="Vidange du <strong>{{ $oilChange->formatted_date }}</strong> - {{ $oilChange->formatted_mileage }}">
                <i class="ti ti-trash me-1"></i>Supprimer
            </a>
        </li>
        @endcan
    </ul>
</div>