<div class="table-responsive">
    <table class="table align-middle">
        <thead class="thead-light">
            <tr>
                <th width="50" class="text-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="select-all">
                    </div>
                </th>
                <th>ID</th>
                <th>Contrat</th>
                <th>Client</th>
                <th>Rôle</th>
                <th>Ordre</th>
                <th width="80">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($contractClients as $item)
            <tr>
                <td class="text-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="{{ $item->id }}">
                    </div>
                </td>
                <td>#{{ $item->id }}</td>
                <td>
                    <a href="{{ route('backoffice.rental-contracts.show', $item->rental_contract_id) }}" class="fw-medium">
                        {{ $item->rentalContract->contract_number ?? 'N/A' }}
                    </a>
                </td>
                <td>
                    <a href="{{ route('backoffice.clients.show', $item->client_id) }}" class="fw-medium">
                        {{ $item->client->first_name ?? '' }} {{ $item->client->last_name ?? '' }}
                    </a>
                    <br><small>{{ $item->client->phone ?? '' }}</small>
                </td>
                <td>
                    <span class="badge {{ $item->role_badge_class }}">
                        {{ $item->role_text }}
                    </span>
                </td>
                <td>{{ $item->order }}</td>
                <td class="text-center">
                    @include('backoffice.contract-clients.partials._actions', ['item' => $item])
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-5">
                    <div class="text-center">
                        <i class="ti ti-users fs-48 text-gray-4 mb-3"></i>
                        <h5 class="mb-2">Aucune relation client-contrat trouvée</h5>
                        <a href="{{ route('backoffice.contract-clients.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-2"></i>Ajouter une relation
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
                document.querySelectorAll('.form-check-input').forEach(cb => {
                    if (cb.id !== 'select-all') {
                        cb.checked = selectAll.checked;
                    }
                });
            });
        }
    });
</script>