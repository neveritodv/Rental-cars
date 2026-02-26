<div class="table-responsive">
    <table class="table align-middle">
        <thead class="thead-light">
            <tr>
                {{-- Case à cocher - visible seulement si permission DELETE --}}
                @can('invoices.general.delete')
                <th width="50" class="text-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="select-all">
                    </div>
                </th>
                @endcan
                <th>N° Facture</th>
                <th>Date</th>
                <th>Client</th>
                <th>Montant TTC</th>
                <th>Statut</th>
                {{-- Colonne Actions - visible seulement si au moins une permission d'action --}}
                @canany(['invoices.general.view', 'invoices.general.edit', 'invoices.general.delete'])
                <th width="80">Actions</th>
                @endcanany
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $invoice)
            <tr>
                {{-- Case à cocher - visible seulement si permission DELETE --}}
                @can('invoices.general.delete')
                <td class="text-center">
                    <div class="form-check">
                        <input class="form-check-input invoice-checkbox" type="checkbox" value="{{ $invoice->id }}">
                    </div>
                </td>
                @endcan
                
                <td>
                    {{-- Lien vers show - contrôlé par permission VIEW --}}
                    @can('invoices.general.view')
                        <a href="{{ route('backoffice.invoices.show', $invoice) }}" class="fw-medium">
                            {{ $invoice->invoice_number }}
                        </a>
                    @else
                        <span class="fw-medium">{{ $invoice->invoice_number }}</span>
                    @endcan
                </td>
                
                <td>{{ $invoice->formatted_invoice_date }}</td>
                
                <td>
                    @if($invoice->client)
                        {{-- Lien vers client - contrôlé par permission VIEW sur clients --}}
                        @can('clients.general.view')
                            <a href="{{ route('backoffice.clients.show', $invoice->client_id) }}" class="fw-medium">
                                {{ $invoice->client->first_name }} {{ $invoice->client->last_name }}
                            </a>
                        @else
                            <span class="fw-medium">{{ $invoice->client->first_name }} {{ $invoice->client->last_name }}</span>
                        @endcan
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
                
                {{-- Actions - visible seulement si au moins une permission d'action --}}
                @canany(['invoices.general.view', 'invoices.general.edit', 'invoices.general.delete'])
                <td class="text-center">
                    @include('backoffice.invoices.partials._actions', ['invoice' => $invoice])
                </td>
                @endcanany
            </tr>
            @empty
            <tr>
                @can('invoices.general.delete')
                <td></td>
                @endcan
                <td colspan="{{ (auth()->user()->can('invoices.general.delete') ? 6 : 5) }}" class="text-center py-5">
                    <div class="text-center">
                        <i class="ti ti-file-invoice-off fs-48 text-gray-4 mb-3"></i>
                        <h5 class="mb-2">Aucune facture trouvée</h5>
                        @can('invoices.general.create')
                            <p class="text-muted mb-3">Commencez par créer une nouvelle facture</p>
                            <a href="{{ route('backoffice.invoices.create') }}" class="btn btn-primary">
                                <i class="ti ti-plus me-2"></i>Nouvelle facture
                            </a>
                        @endcan
                    </div>
                </td>
                @canany(['invoices.general.view', 'invoices.general.edit', 'invoices.general.delete'])
                <td></td>
                @endcanany
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Script pour "Select All" - seulement si permission DELETE --}}
@can('invoices.general.delete')
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
@endcan