<div class="table-responsive">
    <table class="table align-middle">
        <thead class="thead-light">
            <tr>
                <th width="50" class="text-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="select-all">
                    </div>
                </th>
                <th>Facture</th>
                <th>Description</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Total TTC</th>
                <th width="80">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
            <tr>
                <td class="text-center">
                    <div class="form-check">
                        <input class="form-check-input item-checkbox" type="checkbox" value="{{ $item->id }}">
                    </div>
                </td>
                <td>
                    <a href="{{ route('backoffice.invoices.show', $item->invoice_id) }}" class="fw-medium">
                        {{ $item->invoice->invoice_number }}
                    </a>
                </td>
                <td>{{ $item->description }}</td>
                <td>{{ number_format($item->quantity, 2, ',', ' ') }}</td>
                <td>{{ $item->unit_price_ttc ? number_format($item->unit_price_ttc, 2, ',', ' ') . ' ' . $item->invoice->currency : '—' }}</td>
                <td><span class="amount-badge">{{ number_format($item->total_ttc, 2, ',', ' ') }} {{ $item->invoice->currency }}</span></td>
                <td class="text-center">
                    @include('backoffice.invoice-items.partials._actions', ['item' => $item])
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-5">
                    <div class="text-center">
                        <i class="ti ti-file-description fs-48 text-gray-4 mb-3"></i>
                        <h5 class="mb-2">Aucun item trouvé</h5>
                        <p class="text-muted mb-3">Commencez par créer un nouvel item</p>
                        <a href="{{ route('backoffice.invoice-items.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-2"></i>Nouvel item
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
                document.querySelectorAll('.item-checkbox').forEach(cb => {
                    cb.checked = selectAll.checked;
                });
            });
        }
    });
</script>