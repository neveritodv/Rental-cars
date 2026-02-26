@props(['vignette'])

<div class="dropdown">
    <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="ti ti-dots-vertical"></i>
    </button>

    <ul class="dropdown-menu dropdown-menu-end p-2">
        {{-- Voir détails - contrôlé par permission VIEW --}}
        @can('vehicle-vignettes.general.view')
        <li>
            <a class="dropdown-item rounded-1" href="{{ route('backoffice.vehicles.vignettes.show', [$vignette->vehicle_id, $vignette->id]) }}">
                <i class="ti ti-eye me-1"></i>Voir détails
            </a>
        </li>
        @endcan

        {{-- Modifier - contrôlé par permission EDIT --}}
        @can('vehicle-vignettes.general.edit')
        <li>
            <a class="dropdown-item rounded-1" href="{{ route('backoffice.vehicles.vignettes.edit', [$vignette->vehicle_id, $vignette->id]) }}">
                <i class="ti ti-edit me-1"></i>Modifier
            </a>
        </li>
        @endcan

        {{-- Supprimer - contrôlé par permission DELETE --}}
        @can('vehicle-vignettes.general.delete')
        <li>
            <hr class="dropdown-divider">
        </li>
        <li>
            <a class="dropdown-item rounded-1 text-danger" 
               href="#"
               onclick="event.preventDefault(); event.stopPropagation(); document.getElementById('deleteVignetteForm').action = '{{ route('backoffice.vehicles.vignettes.destroy', [$vignette->vehicle_id, $vignette->id]) }}'; document.getElementById('deleteVignetteInfo').innerText = '{{ $vignette->year }} - {{ $vignette->formatted_amount }}'; new bootstrap.Modal(document.getElementById('delete_vignette')).show(); return false;">
                <i class="ti ti-trash me-1"></i>Supprimer
            </a>
        </li>
        @endcan
    </ul>
</div>