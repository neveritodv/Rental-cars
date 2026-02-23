<div class="dropdown">
    <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-haspopup="true">
        <i class="ti ti-dots-vertical"></i>
    </button>

    <ul class="dropdown-menu dropdown-menu-end p-2">
        <li>
            <a class="dropdown-item rounded-1" href="{{ route('backoffice.bookings.show', $booking) }}">
                <i class="ti ti-eye me-2"></i>Voir détails
            </a>
        </li>
        <li>
            <a class="dropdown-item rounded-1" href="{{ route('backoffice.bookings.edit', $booking) }}">
                <i class="ti ti-edit me-2"></i>Modifier
            </a>
        </li>
        @if($booking->status == 'pending')
        <li>
            <a class="dropdown-item rounded-1 text-success" 
               href="{{ route('backoffice.bookings.convert-to-contract', $booking) }}"
               onclick="event.preventDefault(); if(confirm('Convertir cette réservation en contrat ?')) { document.getElementById('convert-form-{{ $booking->id }}').submit(); }">
                <i class="ti ti-file-text me-2"></i>Convertir en contrat
            </a>
            <form id="convert-form-{{ $booking->id }}" action="{{ route('backoffice.bookings.convert-to-contract', $booking) }}" method="POST" style="display: none;">
                @csrf
            </form>
        </li>
        @endif
        <li>
            <hr class="dropdown-divider">
        </li>
        <li>
            <a class="dropdown-item rounded-1 text-danger" 
               href="javascript:void(0);"
               data-bs-toggle="modal" 
               data-bs-target="#delete_booking"
               data-delete-action="{{ route('backoffice.bookings.destroy', $booking) }}"
               data-delete-details="Réservation <strong>#{{ $booking->id }}</strong>">
                <i class="ti ti-trash me-2"></i>Supprimer
            </a>
        </li>
    </ul>
</div>