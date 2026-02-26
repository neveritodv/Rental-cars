<style>
    .btn-icon {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        color: #6c757d;
        background: transparent;
        border: 1px solid transparent;
        transition: all 0.2s;
        cursor: pointer;
    }
    
    .btn-icon:hover {
        background: #f8f9fa;
        border-color: #dee2e6;
        color: #0d6efd;
    }
    
    .btn-icon i {
        font-size: 18px;
    }
    
    .badge-success { background: #d4edda; color: #155724; padding: 0.35rem 0.75rem; border-radius: 50px; font-weight: 500; }
    .badge-danger { background: #f8d7da; color: #721c24; padding: 0.35rem 0.75rem; border-radius: 50px; font-weight: 500; }
    .badge-secondary { background: #e2e3e5; color: #383d41; padding: 0.35rem 0.75rem; border-radius: 50px; font-weight: 500; }
    
    .table-responsive { 
        overflow: visible !important; 
    }
    
    .dropdown-menu {
        z-index: 9999 !important;
        display: block;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s;
    }
    
    .dropdown-menu.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    
    .form-check {
        display: flex;
        justify-content: center;
        margin: 0;
        padding: 0;
    }
    
    .status-badge { 
        padding: 0.35rem 0.75rem; 
        border-radius: 50px; 
        font-weight: 500; 
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .status-badge i {
        font-size: 14px;
    }
    
    .item-key {
        font-family: 'Courier New', monospace;
        font-weight: 600;
        color: #495057;
    }
</style>

<div class="table-responsive">
    <table class="table align-middle">
        <thead class="thead-light">
            <tr>
                {{-- Case à cocher - visible seulement si permission DELETE --}}
                @can('vehicle-control-items.general.delete')
                <th width="50" class="text-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="select-all">
                    </div>
                </th>
                @endcan
                <th>Clé</th>
                <th>Libellé</th>
                <th>Contrôle</th>
                <th>Statut</th>
                <th>Commentaire</th>
                <th>Date</th>
                {{-- Colonne Actions - visible seulement si au moins une permission d'action --}}
                @canany(['vehicle-control-items.general.view', 'vehicle-control-items.general.edit', 'vehicle-control-items.general.delete'])
                <th width="80">Actions</th>
                @endcanany
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
            <tr>
                {{-- Case à cocher - visible seulement si permission DELETE --}}
                @can('vehicle-control-items.general.delete')
                <td class="text-center">
                    <div class="form-check">
                        <input class="form-check-input item-checkbox" type="checkbox" value="{{ $item->id }}">
                    </div>
                </td>
                @endcan
                
                <td>
                    {{-- Lien vers show - contrôlé par permission VIEW --}}
                    @can('vehicle-control-items.general.view')
                        <a href="{{ route('backoffice.control-items.show', $item) }}" class="fw-medium item-key">
                            {{ $item->item_key }}
                        </a>
                    @else
                        <span class="fw-medium item-key">{{ $item->item_key }}</span>
                    @endcan
                </td>
                
                <td>{{ $item->label ?? '—' }}</td>
                
                <td>
                    @if($item->vehicleControl)
                        {{-- Lien vers contrôle - contrôlé par permission VIEW sur contrôles --}}
                        @can('vehicle-controls.general.view')
                            <a href="{{ route('backoffice.controls.show', $item->vehicleControl) }}" class="text-muted small">
                                {{ $item->vehicleControl->control_number }}
                            </a>
                        @else
                            <span class="text-muted small">{{ $item->vehicleControl->control_number }}</span>
                        @endcan
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
                
                {{-- Actions - visible seulement si au moins une permission d'action --}}
                @canany(['vehicle-control-items.general.view', 'vehicle-control-items.general.edit', 'vehicle-control-items.general.delete'])
                <td class="text-center">
                    @include('Backoffice.control-items.partials._actions', ['item' => $item])
                </td>
                @endcanany
            </tr>
            @empty
            <tr>
                @can('vehicle-control-items.general.delete')
                <td></td>
                @endcan
                <td colspan="{{ (auth()->user()->can('vehicle-control-items.general.delete') ? 7 : 6) }}" class="text-center py-5">
                    <div class="text-center">
                        <i class="ti ti-checklist-off fs-48 text-gray-4 mb-3"></i>
                        <h5 class="mb-2">Aucun élément trouvé</h5>
                        @can('vehicle-control-items.general.create')
                            <p class="text-muted mb-3">Commencez par créer un nouvel élément de contrôle</p>
                            <a href="{{ route('backoffice.control-items.create') }}" class="btn btn-primary">
                                <i class="ti ti-plus me-2"></i>Nouvel élément
                            </a>
                        @endcan
                    </div>
                </td>
                @canany(['vehicle-control-items.general.view', 'vehicle-control-items.general.edit', 'vehicle-control-items.general.delete'])
                <td></td>
                @endcanany
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Script pour "Select All" - seulement si permission DELETE --}}
@can('vehicle-control-items.general.delete')
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