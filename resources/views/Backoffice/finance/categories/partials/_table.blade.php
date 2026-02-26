@props(['categories'])

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
    
    .badge-income { background: #d4edda; color: #155724; padding: 0.35rem 0.75rem; border-radius: 50px; font-weight: 500; }
    .badge-expense { background: #f8d7da; color: #721c24; padding: 0.35rem 0.75rem; border-radius: 50px; font-weight: 500; }
    .badge-both { background: #cce5ff; color: #004085; padding: 0.35rem 0.75rem; border-radius: 50px; font-weight: 500; }
    
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
</style>

<div class="table-responsive">
    <table class="table align-middle">
        <thead class="thead-light">
            <tr>
                {{-- Case à cocher - visible seulement si permission DELETE --}}
                @can('transaction-categories.general.delete')
                <th width="50" class="text-center">
                    <div class="form-check form-check-md">
                        <input class="form-check-input" type="checkbox" id="select-all">
                    </div>
                </th>
                @endcan
                
                <th>Nom</th>
                <th>Type</th>
                <th>Transactions</th>
                <th>Total</th>
                
                {{-- Colonne Actions - visible seulement si au moins une permission d'action --}}
                @canany(['transaction-categories.general.view', 'transaction-categories.general.edit', 'transaction-categories.general.delete'])
                <th width="80">Actions</th>
                @endcanany
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $category)
            <tr>
                {{-- Case à cocher - visible seulement si permission DELETE --}}
                @can('transaction-categories.general.delete')
                <td class="text-center">
                    <div class="form-check form-check-md">
                        <input class="form-check-input category-checkbox" type="checkbox" value="{{ $category->id }}">
                    </div>
                </td>
                @endcan
                
                <td>
                    @can('transaction-categories.general.view')
                        <a href="{{ route('backoffice.finance.categories.show', $category) }}" class="fw-medium">
                            {{ $category->name }}
                        </a>
                    @else
                        <span class="fw-medium">{{ $category->name }}</span>
                    @endcan
                </td>
                <td>
                    <span class="badge {{ $category->type_badge_class }}">
                        {{ $category->type_text }}
                    </span>
                </td>
                <td>{{ $category->transactions_count ?? 0 }}</td>
                <td>{{ number_format($category->total_amount ?? 0, 2, ',', ' ') }} MAD</td>
                
                {{-- Actions - visible seulement si au moins une permission d'action --}}
                @canany(['transaction-categories.general.view', 'transaction-categories.general.edit', 'transaction-categories.general.delete'])
                <td class="text-center">
                    @include('backoffice.finance.categories.partials._actions', ['category' => $category])
                </td>
                @endcanany
            </tr>
            @empty
            <tr>
                @can('transaction-categories.general.delete')
                <td></td>
                @endcan
                
                <td colspan="{{ auth()->user()->can('transaction-categories.general.delete') ? 5 : 4 }}" class="text-center py-5">
                    <div class="text-center">
                        <i class="ti ti-category fs-48 text-gray-4 mb-3"></i>
                        <h5 class="mb-2">Aucune catégorie trouvée</h5>
                        
                        @can('transaction-categories.general.create')
                            <p class="text-muted mb-3">Commencez par créer une nouvelle catégorie</p>
                            <a href="{{ route('backoffice.finance.categories.create') }}" class="btn btn-primary">
                                <i class="ti ti-plus me-2"></i>Nouvelle catégorie
                            </a>
                        @endcan
                    </div>
                </td>
                
                @canany(['transaction-categories.general.view', 'transaction-categories.general.edit', 'transaction-categories.general.delete'])
                <td></td>
                @endcanany
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Script pour "Select All" - seulement si permission DELETE --}}
@can('transaction-categories.general.delete')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                document.querySelectorAll('.category-checkbox').forEach(cb => {
                    cb.checked = selectAll.checked;
                });
            });
        }
    });
</script>
@endcan