<div class="table-responsive">
    <table class="table align-middle">
        <thead class="thead-light">
            <tr>
                {{-- Case à cocher - visible seulement si permission DELETE --}}
                @can('payments.general.delete')
                <th width="50" class="text-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="select-all">
                    </div>
                </th>
                @endcan
                <th>Date</th>
                <th>Référence</th>
                <th>Facture/Contrat</th>
                <th>Méthode</th>
                <th>Montant</th>
                <th>Statut</th>
                {{-- Colonne Actions - visible seulement si au moins une permission d'action --}}
                @canany(['payments.general.view', 'payments.general.edit', 'payments.general.delete'])
                <th width="80">Actions</th>
                @endcanany
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
            <tr>
                {{-- Case à cocher - visible seulement si permission DELETE --}}
                @can('payments.general.delete')
                <td class="text-center">
                    <div class="form-check">
                        <input class="form-check-input payment-checkbox" type="checkbox" value="{{ $payment->id }}">
                    </div>
                </td>
                @endcan
                
                <td>{{ $payment->formatted_payment_date }}</td>
                
                <td>
                    {{-- Lien vers show - contrôlé par permission VIEW --}}
                    @can('payments.general.view')
                        <a href="{{ route('backoffice.payments.show', $payment) }}" class="fw-medium">
                            {{ $payment->reference ?? '—' }}
                        </a>
                    @else
                        <span class="fw-medium">{{ $payment->reference ?? '—' }}</span>
                    @endcan
                </td>
                
                <td>
                    @if($payment->invoice)
                        {{-- Lien vers facture - contrôlé par permission VIEW sur factures --}}
                        @can('invoices.general.view')
                            <a href="{{ route('backoffice.invoices.show', $payment->invoice_id) }}" class="fw-medium">
                                Facture {{ $payment->invoice->invoice_number }}
                            </a>
                        @else
                            <span class="fw-medium">Facture {{ $payment->invoice->invoice_number }}</span>
                        @endcan
                    @elseif($payment->rentalContract)
                        {{-- Lien vers contrat - contrôlé par permission VIEW sur contrats --}}
                        @can('rental-contracts.general.view')
                            <a href="{{ route('backoffice.rental-contracts.show', $payment->rental_contract_id) }}" class="fw-medium">
                                Contrat {{ $payment->rentalContract->contract_number }}
                            </a>
                        @else
                            <span class="fw-medium">Contrat {{ $payment->rentalContract->contract_number }}</span>
                        @endcan
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                
                <td>
                    <span class="badge {{ $payment->method_badge_class }}">
                        {{ $payment->method_text }}
                    </span>
                </td>
                
                <td>
                    <span class="amount-badge">{{ $payment->formatted_amount }}</span>
                </td>
                
                <td>
                    <span class="badge {{ $payment->status_badge_class }}">
                        {{ $payment->status_text }}
                    </span>
                </td>
                
                {{-- Actions - visible seulement si au moins une permission d'action --}}
                @canany(['payments.general.view', 'payments.general.edit', 'payments.general.delete'])
                <td class="text-center">
                    @include('backoffice.payments.partials._actions', ['payment' => $payment])
                </td>
                @endcanany
            </tr>
            @empty
            <tr>
                @can('payments.general.delete')
                <td></td>
                @endcan
                <td colspan="{{ (auth()->user()->can('payments.general.delete') ? 7 : 6) }}" class="text-center py-5">
                    <div class="text-center">
                        <i class="ti ti-currency-dollar-off fs-48 text-gray-4 mb-3"></i>
                        <h5 class="mb-2">Aucun paiement trouvé</h5>
                        @can('payments.general.create')
                            <p class="text-muted mb-3">Commencez par créer un nouveau paiement</p>
                            <a href="{{ route('backoffice.payments.create') }}" class="btn btn-primary">
                                <i class="ti ti-plus me-2"></i>Nouveau paiement
                            </a>
                        @endcan
                    </div>
                </td>
                @canany(['payments.general.view', 'payments.general.edit', 'payments.general.delete'])
                <td></td>
                @endcanany
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Script pour "Select All" - seulement si permission DELETE --}}
@can('payments.general.delete')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                document.querySelectorAll('.payment-checkbox').forEach(cb => {
                    cb.checked = selectAll.checked;
                });
            });
        }
    });
</script>
@endcan