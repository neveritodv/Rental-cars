<div class="table-responsive">
    <table class="table align-middle">
        <thead class="thead-light">
            <tr>
                <th width="50" class="text-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="select-all">
                    </div>
                </th>
                <th>N° Facture</th>
                <th>Date</th>
                <th>Client</th>
                <th>Montant TTC</th>
                <th>Statut</th>
                <th width="80">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $invoice)
            <tr>
                <td class="text-center">
                    <div class="form-check">
                        <input class="form-check-input invoice-checkbox" type="checkbox" value="{{ $invoice->id }}">
                    </div>
                </td>
                <td>
                    <a href="{{ route('backoffice.invoices.show', $invoice) }}" class="fw-medium">
                        {{ $invoice->invoice_number }}
                    </a>
                </td>
                <td>{{ $invoice->formatted_invoice_date }}</td>
                <td>
                    @if($invoice->client)
                        <a href="{{ route('backoffice.clients.show', $invoice->client_id) }}" class="fw-medium">
                            {{ $invoice->client->first_name }} {{ $invoice->client->last_name }}
                        </a>
                    @elseif($invoice->company_name)
                        {{ $invoice->company_name }}
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                <td>
                    <span class="amount-badge">{{ $invoice->formatted_total_ttc }}</span>
                </td>
                <td>
                    <span class="badge {{ $invoice->status_badge_class }}">
                        {{ $invoice->status_text }}
                    </span>
                </td>
                <td class="text-center">
                    @include('backoffice.invoices.partials._actions', ['invoice' => $invoice])
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-5">
                    <div class="text-center">
                        <i class="ti ti-file-invoice fs-48 text-gray-4 mb-3"></i>
                        <h5 class="mb-2">Aucune facture trouvée</h5>
                        <p class="text-muted mb-3">Commencez par créer une nouvelle facture</p>
                        <a href="{{ route('backoffice.invoices.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-2"></i>Nouvelle facture
                        </a>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                document.querySelectorAll('.invoice-checkbox').forEach(cb => {
                    cb.checked = selectAll.checked;
                });
            });
        }
    });
</script>