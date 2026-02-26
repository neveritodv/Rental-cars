@props(['insurance'])

<div class="dropdown">
    <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="ti ti-dots-vertical"></i>
    </button>

    <ul class="dropdown-menu dropdown-menu-end p-2">
        {{-- Voir détails - contrôlé par permission VIEW --}}
        @can('vehicle-insurances.general.view')
        <li>
            <a class="dropdown-item rounded-1" href="{{ route('backoffice.vehicles.insurances.show', [$insurance->vehicle_id, $insurance->id]) }}">
                <i class="ti ti-eye me-1"></i>Voir détails
            </a>
        </li>
        @endcan

        {{-- Modifier - contrôlé par permission EDIT --}}
        @can('vehicle-insurances.general.edit')
        <li>
            <a class="dropdown-item rounded-1" href="{{ route('backoffice.vehicles.insurances.edit', [$insurance->vehicle_id, $insurance->id]) }}">
                <i class="ti ti-edit me-1"></i>Modifier
            </a>
        </li>
        @endcan

        {{-- Supprimer - contrôlé par permission DELETE --}}
        @can('vehicle-insurances.general.delete')
        <li>
            <hr class="dropdown-divider">
        </li>
        <li>
            <a class="dropdown-item rounded-1 text-danger" 
               href="javascript:void(0);"
               data-bs-toggle="modal" 
               data-bs-target="#delete_insurance"
               data-delete-action="{{ route('backoffice.vehicles.insurances.destroy', [$insurance->vehicle_id, $insurance->id]) }}"
               data-delete-details="Assurance <strong>{{ $insurance->company_name ?? 'N/C' }}</strong> - {{ $insurance->formatted_amount }}">
                <i class="ti ti-trash me-1"></i>Supprimer
            </a>
        </li>
        @endcan
    </ul>
</div>