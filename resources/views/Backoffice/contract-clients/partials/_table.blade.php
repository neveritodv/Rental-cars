<div class="table-responsive">
    <table class="table align-middle">
        <thead class="thead-light">
            <tr>
                {{-- Case à cocher - visible seulement si permission DELETE --}}
                @can('contract-clients.general.delete')
                <th width="50" class="text-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="select-all">
                    </div>
                </th>
                @endcan
                <th>ID</th>
                <th>Contrat</th>
                <th>Client</th>
                <th>Rôle</th>
                <th>Ordre</th>
                {{-- Colonne Actions - visible seulement si au moins une permission d'action --}}
                @canany(['contract-clients.general.view', 'contract-clients.general.edit', 'contract-clients.general.delete'])
                <th width="80">Actions</th>
                @endcanany
            </tr>
        </thead>
        <tbody>
            @forelse($contractClients as $item)
            <tr>
                {{-- Case à cocher - visible seulement si permission DELETE --}}
                @can('contract-clients.general.delete')
                <td class="text-center">
                    <div class="form-check">
                        <input class="form-check-input item-checkbox" type="checkbox" value="{{ $item->id }}">
                    </div>
                </td>
                @endcan
                
                <td>#{{ $item->id }}</td>
                
                <td>
                    {{-- Lien vers contrat - visible seulement si permission VIEW sur contrats --}}
                    @can('rental-contracts.general.view')
                        <a href="{{ route('backoffice.rental-contracts.show', $item->rental_contract_id) }}" class="fw-medium">
                            {{ $item->rentalContract->contract_number ?? 'N/A' }}
                        </a>
                    @else
                        <span class="fw-medium">{{ $item->rentalContract->contract_number ?? 'N/A' }}</span>
                    @endcan
                </td>
                
                <td>
                    {{-- Lien vers client - visible seulement si permission VIEW sur clients --}}
                    @can('clients.general.view')
                        <a href="{{ route('backoffice.clients.show', $item->client_id) }}" class="fw-medium">
                            {{ $item->client->first_name ?? '' }} {{ $item->client->last_name ?? '' }}
                        </a>
                    @else
                        <span class="fw-medium">{{ $item->client->first_name ?? '' }} {{ $item->client->last_name ?? '' }}</span>
                    @endcan
                    <br><small>{{ $item->client->phone ?? '' }}</small>
                </td>
                
                <td>
                    <span class="badge {{ $item->role_badge_class }}">
                        {{ $item->role_text }}
                    </span>
                </td>
                
                <td>{{ $item->order }}</td>
                
                {{-- Actions - visible seulement si au moins une permission d'action --}}
                @canany(['contract-clients.general.view', 'contract-clients.general.edit', 'contract-clients.general.delete'])
                <td class="text-center">
                    @include('backoffice.contract-clients.partials._actions', ['item' => $item])
                </td>
                @endcanany
            </tr>
            @empty
            <tr>
                {{-- Cellule vide pour la case à cocher si elle existe --}}
                @can('contract-clients.general.delete')
                <td></td>
                @endcan
                
                {{-- Message vide - ajuster le colspan en fonction des permissions --}}
                <td colspan="{{ (auth()->user()->can('contract-clients.general.delete') ? 6 : 5) }}" class="text-center py-5">
                    <div class="text-center">
                        <i class="ti ti-users-off fs-48 text-gray-4 mb-3"></i>
                        <h5 class="mb-2">Aucune relation client-contrat trouvée</h5>
                        @can('contract-clients.general.create')
                            <a href="{{ route('backoffice.contract-clients.create') }}" class="btn btn-primary">
                                <i class="ti ti-plus me-2"></i>Ajouter une relation
                            </a>
                        @endcan
                    </div>
                </td>
                
                {{-- Cellule vide pour la colonne Actions si elle existe --}}
                @canany(['contract-clients.general.view', 'contract-clients.general.edit', 'contract-clients.general.delete'])
                <td></td>
                @endcanany
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Script pour "Select All" - seulement si permission DELETE --}}
@can('contract-clients.general.delete')
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