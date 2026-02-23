<div class="table-responsive">
    <table class="table align-middle">
        <thead class="thead-light">
            <tr>
                <th width="50" class="text-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="select-all">
                    </div>
                </th>
                <th>Clé</th>
                <th>Libellé</th>
                <th>Contrôle</th>
                <th>Statut</th>
                <th>Commentaire</th>
                <th>Date</th>
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
                    <a href="{{ route('backoffice.control-items.show', $item) }}" class="fw-medium item-key">
                        {{ $item->item_key }}
                    </a>
                </td>
                <td>{{ $item->label ?? '—' }}</td>
                <td>
                    @if($item->vehicleControl)
                        <span class="text-muted small">
                            {{ $item->vehicleControl->control_number }}
                        </span>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                <td>
                    <span class="status-badge {{ $item->status_badge_class }}">
                        <i class="{{ $item->status_icon }}"></i>
                        {{ $item->status_text }}
                    </span>
                </td>
                <td>
                    @if($item->comment)
                        <span class="text-muted small" title="{{ $item->comment }}">
                            {{ Str::limit($item->comment, 30) }}
                        </span>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                <td>
                    <div class="d-flex flex-column">
                        <span>{{ $item->created_at->format('d/m/Y') }}</span>
                        <small class="text-muted">{{ $item->created_at->format('H:i') }}</small>
                    </div>
                </td>
                <td class="text-center">
                    @include('Backoffice.control-items.partials._actions', ['item' => $item])
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center py-5">
                    <div class="text-center">
                        <i class="ti ti-checklist fs-48 text-gray-4 mb-3"></i>
                        <h5 class="mb-2">Aucun élément trouvé</h5>
                        <p class="text-muted mb-3">Commencez par créer un nouvel élément de contrôle</p>
                        <a href="{{ route('backoffice.control-items.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-2"></i>Nouvel élément
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