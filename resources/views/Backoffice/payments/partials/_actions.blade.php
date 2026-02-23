<div class="dropdown">
    <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-haspopup="true">
        <i class="ti ti-dots-vertical"></i>
    </button>

    <ul class="dropdown-menu dropdown-menu-end p-2">
        <li>
            <a class="dropdown-item rounded-1" href="{{ route('backoffice.payments.show', $payment) }}">
                <i class="ti ti-eye me-2"></i>Voir détails
            </a>
        </li>
        <li>
            <a class="dropdown-item rounded-1" href="{{ route('backoffice.payments.edit', $payment) }}">
                <i class="ti ti-edit me-2"></i>Modifier
            </a>
        </li>
        @if($payment->status == 'pending')
        <li>
            <a class="dropdown-item rounded-1 text-success" 
               href="javascript:void(0);"
               onclick="event.preventDefault(); if(confirm('Confirmer ce paiement ?')) { document.getElementById('confirm-payment-{{ $payment->id }}').submit(); }">
                <i class="ti ti-check me-2"></i>Confirmer
            </a>
            <form id="confirm-payment-{{ $payment->id }}" action="{{ route('backoffice.payments.status', $payment) }}" method="POST" style="display: none;">
                @csrf
                <input type="hidden" name="status" value="confirmed">
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
               data-bs-target="#delete_payment"
               data-delete-action="{{ route('backoffice.payments.destroy', $payment) }}"
               data-delete-details="Paiement <strong>#{{ $payment->id }}</strong>">
                <i class="ti ti-trash me-2"></i>Supprimer
            </a>
        </li>
    </ul>
</div>