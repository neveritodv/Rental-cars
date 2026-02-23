<div class="dropdown">
    <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="ti ti-dots-vertical"></i>
    </button>

    <ul class="dropdown-menu dropdown-menu-end p-2">
        <li>
            <a class="dropdown-item rounded-1" href="{{ route('backoffice.vehicles.vignettes.show', [$vignette->vehicle_id, $vignette->id]) }}">
                <i class="ti ti-eye me-1"></i>Voir détails
            </a>
        </li>
        <li>
            <a class="dropdown-item rounded-1" href="{{ route('backoffice.vehicles.vignettes.edit', [$vignette->vehicle_id, $vignette->id]) }}">
                <i class="ti ti-edit me-1"></i>Modifier
            </a>
        </li>
        <li>
            <hr class="dropdown-divider">
        </li>
        <li>
            <a class="dropdown-item rounded-1 text-danger" 
               href="#"
               data-bs-toggle="modal" 
               data-bs-target="#delete_vignette"
               data-delete-url="{{ route('backoffice.vehicles.vignettes.destroy', [$vignette->vehicle_id, $vignette->id]) }}"
               data-vignette-date="{{ $vignette->formatted_date }}"
               data-vignette-year="{{ $vignette->year }}"
               data-vignette-amount="{{ $vignette->formatted_amount }}">
                <i class="ti ti-trash me-1"></i>Supprimer
            </a>
        </li>
    </ul>
</div>