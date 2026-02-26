@props(['category'])

<div class="dropdown">
    <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-haspopup="true">
        <i class="ti ti-dots-vertical"></i>
    </button>

    <ul class="dropdown-menu dropdown-menu-end p-2">
        {{-- Voir détails - contrôlé par permission VIEW --}}
        @can('transaction-categories.general.view')
        <li>
            <a class="dropdown-item rounded-1" href="{{ route('backoffice.finance.categories.show', $category) }}">
                <i class="ti ti-eye me-2"></i>Voir détails
            </a>
        </li>
        @endcan

        {{-- Modifier - contrôlé par permission EDIT --}}
        @can('transaction-categories.general.edit')
        <li>
            <a class="dropdown-item rounded-1" href="{{ route('backoffice.finance.categories.edit', $category) }}">
                <i class="ti ti-edit me-2"></i>Modifier
            </a>
        </li>
        @endcan

        {{-- Supprimer - contrôlé par permission DELETE --}}
        @can('transaction-categories.general.delete')
        <li>
            <hr class="dropdown-divider">
        </li>
        <li>
            <a class="dropdown-item rounded-1 text-danger" 
               href="#"
               onclick="event.preventDefault(); event.stopPropagation(); document.getElementById('deleteCategoryForm').action = '{{ route('backoffice.finance.categories.destroy', $category) }}'; document.getElementById('deleteCategoryName').innerText = '{{ $category->name }}'; new bootstrap.Modal(document.getElementById('delete_category')).show(); return false;">
                <i class="ti ti-trash me-2"></i>Supprimer
            </a>
        </li>
        @endcan
    </ul>
</div>