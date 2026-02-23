<div class="table-responsive">
    <table class="table align-middle">
        <thead class="thead-light">
            <tr>
                <th width="50" class="text-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="select-all">
                    </div>
                </th>
                <th>Date</th>
                <th>Référence</th>
                <th>Facture/Contrat</th>
                <th>Méthode</th>
                <th>Montant</th>
                <th>Statut</th>
                <th width="80">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
            <tr>
                <td class="text-center">
                    <div class="form-check">
                        <input class="form-check-input payment-checkbox" type="checkbox" value="{{ $payment->id }}">
                    </div>
                </td>
                <td>{{ $payment->formatted_payment_date }}</td>
                <td>
                    <a href="{{ route('backoffice.payments.show', $payment) }}" class="fw-medium">
                        {{ $payment->reference ?? '—' }}
                    </a>
                </td>
                <td>
                    @if($payment->invoice)
                        <a href="{{ route('backoffice.invoices.show', $payment->invoice_id) }}" class="fw-medium">
                            Facture {{ $payment->invoice->invoice_number }}
                        </a>
                    @elseif($payment->rentalContract)
                        <a href="{{ route('backoffice.rental-contracts.show', $payment->rental_contract_id) }}" class="fw-medium">
                            Contrat {{ $payment->rentalContract->contract_number }}
                        </a>
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
                <td class="text-center">
                    @include('backoffice.payments.partials._actions', ['payment' => $payment])
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center py-5">
                    <div class="text-center">
                        <i class="ti ti-currency-dollar fs-48 text-gray-4 mb-3"></i>
                        <h5 class="mb-2">Aucun paiement trouvé</h5>
                        <p class="text-muted mb-3">Commencez par créer un nouveau paiement</p>
                        <a href="{{ route('backoffice.payments.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-2"></i>Nouveau paiement
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
                document.querySelectorAll('.payment-checkbox').forEach(cb => {
                    cb.checked = selectAll.checked;
                });
            });
        }
    });
</script>