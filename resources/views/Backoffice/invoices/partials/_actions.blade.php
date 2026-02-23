<div class="dropdown">
    <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-haspopup="true">
        <i class="ti ti-dots-vertical"></i>
    </button>

    <ul class="dropdown-menu dropdown-menu-end p-2">
        <li>
            <a class="dropdown-item rounded-1" href="{{ route('backoffice.invoices.show', $invoice) }}">
                <i class="ti ti-eye me-2"></i>Voir détails
            </a>
        </li>
        <li>
            <a class="dropdown-item rounded-1" href="{{ route('backoffice.invoices.edit', $invoice) }}">
                <i class="ti ti-edit me-2"></i>Modifier
            </a>
        </li>
        
        <!-- Export PDF using multiple template -->
        <li>
            <form method="POST" action="{{ route('backoffice.invoices.pdf.multiple') }}" style="display: block;">
                @csrf
                <input type="hidden" name="ids[]" value="{{ $invoice->id }}">
                <button type="submit" class="dropdown-item rounded-1 text-primary" style="background: none; border: none; width: 100%; text-align: left;">
                    <i class="ti ti-file-export me-2"></i>Exporter PDF
                </button>
            </form>
        </li>
        
        @if($invoice->status == 'draft')
        <li>
            <a class="dropdown-item rounded-1 text-info" 
               href="javascript:void(0);"
               onclick="event.preventDefault(); if(confirm('Marquer cette facture comme envoyée ?')) { document.getElementById('mark-sent-{{ $invoice->id }}').submit(); }">
                <i class="ti ti-send me-2"></i>Marquer comme envoyée
            </a>
            <form id="mark-sent-{{ $invoice->id }}" action="{{ route('backoffice.invoices.status', $invoice) }}" method="POST" style="display: none;">
                @csrf
                <input type="hidden" name="status" value="sent">
            </form>
        </li>
        @endif
        
        @if($invoice->status == 'sent' || $invoice->status == 'partially_paid')
        <li>
            <a class="dropdown-item rounded-1 text-success" 
               href="javascript:void(0);"
               onclick="event.preventDefault(); if(confirm('Marquer cette facture comme payée ?')) { document.getElementById('mark-paid-{{ $invoice->id }}').submit(); }">
                <i class="ti ti-check me-2"></i>Marquer comme payée
            </a>
            <form id="mark-paid-{{ $invoice->id }}" action="{{ route('backoffice.invoices.status', $invoice) }}" method="POST" style="display: none;">
                @csrf
                <input type="hidden" name="status" value="paid">
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
               data-bs-target="#delete_invoice"
               data-delete-action="{{ route('backoffice.invoices.destroy', $invoice) }}"
               data-delete-details="Facture <strong>{{ $invoice->invoice_number }}</strong>">
                <i class="ti ti-trash me-2"></i>Supprimer
            </a>
        </li>
    </ul>
</div>