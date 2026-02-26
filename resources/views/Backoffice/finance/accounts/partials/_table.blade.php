@props(['accounts'])

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
    
    .badge-info { background: #cce5ff; color: #004085; padding: 0.35rem 0.75rem; border-radius: 50px; font-weight: 500; }
    .badge-success { background: #d4edda; color: #155724; padding: 0.35rem 0.75rem; border-radius: 50px; font-weight: 500; }
    .badge-secondary { background: #e2e3e5; color: #383d41; padding: 0.35rem 0.75rem; border-radius: 50px; font-weight: 500; }
    
    .table-responsive, 
    .custom-datatable-filter, 
    .dataTables_wrapper {
        overflow: visible !important;
    }
    
    .dropdown-menu {
        z-index: 9999 !important;
    }
    
    .form-check {
        display: flex;
        justify-content: center;
        margin: 0;
        padding: 0;
    }
    
    .balance-badge { 
        background: #e8f5e9; 
        color: #2e7d32; 
        padding: 0.35rem 0.75rem; 
        border-radius: 50px; 
        font-weight: 500; 
        white-space: nowrap;
    }
    
    .default-badge {
        background: #ffc107;
        color: #856404;
        padding: 0.25rem 0.5rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 500;
        display: inline-block;
    }
</style>

<div class="table-responsive">
    <table class="table align-middle">
        <thead class="thead-light">
            <tr>
                {{-- Case à cocher - visible seulement si permission DELETE --}}
                @can('financial-accounts.general.delete')
                <th width="50" class="text-center">
                    <div class="form-check form-check-md">
                        <input class="form-check-input" type="checkbox" id="select-all">
                    </div>
                </th>
                @endcan
                
                <th>Nom</th>
                <th>Type</th>
                <th>RIB</th>
                <th>Solde initial</th>
                <th>Solde actuel</th>
                <th>Défaut</th>
                
                {{-- Colonne Actions - visible seulement si au moins une permission d'action --}}
                @canany(['financial-accounts.general.view', 'financial-accounts.general.edit', 'financial-accounts.general.delete'])
                <th width="80">Actions</th>
                @endcanany
            </tr>
        </thead>
        <tbody>
            @forelse($accounts as $account)
            <tr>
                {{-- Case à cocher - visible seulement si permission DELETE --}}
                @can('financial-accounts.general.delete')
                <td class="text-center">
                    <div class="form-check form-check-md">
                        <input class="form-check-input account-checkbox" type="checkbox" value="{{ $account->id }}">
                    </div>
                </td>
                @endcan
                
                <td>
                    @can('financial-accounts.general.view')
                        <a href="{{ route('backoffice.finance.accounts.show', $account) }}" class="fw-medium">
                            {{ $account->name }}
                        </a>
                    @else
                        <span class="fw-medium">{{ $account->name }}</span>
                    @endcan
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
                
                {{-- Actions - visible seulement si au moins une permission d'action --}}
                @canany(['financial-accounts.general.view', 'financial-accounts.general.edit', 'financial-accounts.general.delete'])
                <td class="text-center">
                    @include('backoffice.finance.accounts.partials._actions', ['account' => $account])
                </td>
                @endcanany
            </tr>
            @empty
            <tr>
                @can('financial-accounts.general.delete')
                <td></td>
                @endcan
                
                <td colspan="{{ auth()->user()->can('financial-accounts.general.delete') ? 7 : 6 }}" class="text-center py-5">
                    <div class="text-center">
                        <i class="ti ti-building-bank fs-48 text-gray-4 mb-3"></i>
                        <h5 class="mb-2">Aucun compte trouvé</h5>
                        
                        @can('financial-accounts.general.create')
                            <p class="text-muted mb-3">Commencez par créer un nouveau compte financier</p>
                            <a href="{{ route('backoffice.finance.accounts.create') }}" class="btn btn-primary">
                                <i class="ti ti-plus me-2"></i>Nouveau compte
                            </a>
                        @endcan
                    </div>
                </td>
                
                @canany(['financial-accounts.general.view', 'financial-accounts.general.edit', 'financial-accounts.general.delete'])
                <td></td>
                @endcanany
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Script pour "Select All" - seulement si permission DELETE --}}
@can('financial-accounts.general.delete')
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
@endcan