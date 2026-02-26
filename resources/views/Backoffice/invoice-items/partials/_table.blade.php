<div class="table-responsive">
    <table class="table align-middle">
        <thead class="thead-light">
            <tr>
                {{-- Case à cocher - visible seulement si permission DELETE --}}
                @can('invoice-items.general.delete')
                <th width="50" class="text-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="select-all">
                    </div>
                </th>
                @endcan
                <th>Facture</th>
                <th>Description</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Total TTC</th>
                {{-- Colonne Actions - visible seulement si au moins une permission d'action --}}
                @canany(['invoice-items.general.view', 'invoice-items.general.edit', 'invoice-items.general.delete'])
                <th width="80">Actions</th>
                @endcanany
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
            <tr>
                {{-- Case à cocher - visible seulement si permission DELETE --}}
                @can('invoice-items.general.delete')
                <td class="text-center">
                    <div class="form-check">
                        <input class="form-check-input item-checkbox" type="checkbox" value="{{ $item->id }}">
                    </div>
                </td>
                @endcan
                
                <td>
                    {{-- Lien vers facture - contrôlé par permission VIEW sur factures --}}
                    @can('invoices.general.view')
                        <a href="{{ route('backoffice.invoices.show', $item->invoice_id) }}" class="fw-medium">
                            {{ $item->invoice->invoice_number }}
                        </a>
                    @else
                        <span class="fw-medium">{{ $item->invoice->invoice_number }}</span>
                    @endcan
                </td>
                
                <td>{{ $item->description }}</td>
                
                <td>{{ number_format($item->quantity, 2, ',', ' ') }}</td>
                
                <td>{{ $item->unit_price_ttc ? number_format($item->unit_price_ttc, 2, ',', ' ') . ' ' . $item->invoice->currency : '—' }}</td>
                
                <td><span class="amount-badge">{{ number_format($item->total_ttc, 2, ',', ' ') }} {{ $item->invoice->currency }}</span></td>
                
                {{-- Actions - visible seulement si au moins une permission d'action --}}
                @canany(['invoice-items.general.view', 'invoice-items.general.edit', 'invoice-items.general.delete'])
                <td class="text-center">
                    @include('backoffice.invoice-items.partials._actions', ['item' => $item])
                </td>
                @endcanany
            </tr>
            @empty
            <tr>
                @can('invoice-items.general.delete')
                <td></td>
                @endcan
                <td colspan="{{ (auth()->user()->can('invoice-items.general.delete') ? 6 : 5) }}" class="text-center py-5">
                    <div class="text-center">
                        <i class="ti ti-file-description-off fs-48 text-gray-4 mb-3"></i>
                        <h5 class="mb-2">Aucun item trouvé</h5>
                        @can('invoice-items.general.create')
                            <p class="text-muted mb-3">Commencez par créer un nouvel item</p>
                            <a href="{{ route('backoffice.invoice-items.create') }}" class="btn btn-primary">
                                <i class="ti ti-plus me-2"></i>Nouvel item
                            </a>
                        @endcan
                    </div>
                </td>
                @canany(['invoice-items.general.view', 'invoice-items.general.edit', 'invoice-items.general.delete'])
                <td></td>
                @endcanany
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Script pour "Select All" - seulement si permission DELETE --}}
@can('invoice-items.general.delete')
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
@endcan