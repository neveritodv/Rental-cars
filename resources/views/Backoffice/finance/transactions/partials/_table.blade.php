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
                <th>Description</th>
                <th>Compte</th>
                <th>Catégorie</th>
                <th>Référence</th>
                <th>Montant</th>
                <th width="80">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaction)
            <tr>
                <td class="text-center">
                    <div class="form-check">
                        <input class="form-check-input transaction-checkbox" type="checkbox" value="{{ $transaction->id }}">
                    </div>
                </td>
                <td>{{ $transaction->formatted_date }}</td>
                <td>
                    <a href="{{ route('backoffice.finance.transactions.show', $transaction) }}" class="fw-medium">
                        {{ $transaction->description ?? '—' }}
                    </a>
                </td>
                <td>
                    @if($transaction->account)
                        <a href="{{ route('backoffice.finance.accounts.show', $transaction->financial_account_id) }}">
                            {{ $transaction->account->name }}
                        </a>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                <td>
                    @if($transaction->category)
                        {{ $transaction->category->name }}
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                <td>{{ $transaction->reference ?? '—' }}</td>
                <td>
                    <span class="{{ $transaction->type === 'income' ? 'amount-income' : 'amount-expense' }}">
                        {{ $transaction->formatted_amount }}
                    </span>
                </td>
                <td class="text-center">
                    @include('backoffice.finance.transactions.partials._actions', ['transaction' => $transaction])
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center py-5">
                    <div class="text-center">
                        <i class="ti ti-transfer fs-48 text-gray-4 mb-3"></i>
                        <h5 class="mb-2">Aucune transaction trouvée</h5>
                        <p class="text-muted mb-3">Commencez par créer une nouvelle transaction</p>
                        <a href="{{ route('backoffice.finance.transactions.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-2"></i>Nouvelle transaction
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
                document.querySelectorAll('.transaction-checkbox').forEach(cb => {
                    cb.checked = selectAll.checked;
                });
            });
        }
    });
</script>