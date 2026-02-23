    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="thead-light">
                <tr>
                    <th width="50" class="text-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="select-all">
                        </div>
                    </th>
                    <th>Nom</th>
                    <th>Type</th>
                    <th>RIB</th>
                    <th>Solde initial</th>
                    <th>Solde actuel</th>
                    <th>Défaut</th>
                    <th width="80">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($accounts as $account)
                <tr>
                    <td class="text-center">
                        <div class="form-check">
                            <input class="form-check-input account-checkbox" type="checkbox" value="{{ $account->id }}">
                        </div>
                    </td>
                    <td>
                        <a href="{{ route('backoffice.finance.accounts.show', $account) }}" class="fw-medium">
                            {{ $account->name }}
                        </a>
                    </td>
                    <td>
                        <span class="badge {{ $account->type_badge_class }}">
                            {{ $account->type_text }}
                        </span>
                    </td>
                    <td>{{ $account->rib ?? '—' }}</td>
                    <td>
                        <span class="balance-badge">{{ $account->formatted_initial_balance }}</span>
                    </td>
                    <td>
                        <span class="balance-badge">{{ $account->formatted_current_balance }}</span>
                    </td>
                    <td>
                        @if($account->is_default)
                            <span class="default-badge">
                                <i class="ti ti-check me-1"></i>Défaut
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @include('backoffice.finance.accounts.partials._actions', ['account' => $account])
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <div class="text-center">
                            <i class="ti ti-building-bank fs-48 text-gray-4 mb-3"></i>
                            <h5 class="mb-2">Aucun compte trouvé</h5>
                            <p class="text-muted mb-3">Commencez par créer un nouveau compte financier</p>
                            <a href="{{ route('backoffice.finance.accounts.create') }}" class="btn btn-primary">
                                <i class="ti ti-plus me-2"></i>Nouveau compte
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
                    document.querySelectorAll('.account-checkbox').forEach(cb => {
                        cb.checked = selectAll.checked;
                    });
                });
            }
        });
    </script>