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
                <th>Transactions</th>
                <th>Total</th>
                <th width="80">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $category)
            <tr>
                <td class="text-center">
                    <div class="form-check">
                        <input class="form-check-input category-checkbox" type="checkbox" value="{{ $category->id }}">
                    </div>
                </td>
                <td>
                    <a href="{{ route('backoffice.finance.categories.show', $category) }}" class="fw-medium">
                        {{ $category->name }}
                    </a>
                </td>
                <td>
                    <span class="badge {{ $category->type_badge_class }}">
                        {{ $category->type_text }}
                    </span>
                </td>
                <td>{{ $category->transactions_count }}</td>
                <td>{{ number_format($category->total_amount, 2, ',', ' ') }} MAD</td>
                <td class="text-center">
                    @include('backoffice.finance.categories.partials._actions', ['category' => $category])
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center py-5">
                    <div class="text-center">
                        <i class="ti ti-category fs-48 text-gray-4 mb-3"></i>
                        <h5 class="mb-2">Aucune catégorie trouvée</h5>
                        <p class="text-muted mb-3">Commencez par créer une nouvelle catégorie</p>
                        <a href="{{ route('backoffice.finance.categories.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-2"></i>Nouvelle catégorie
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
                document.querySelectorAll('.category-checkbox').forEach(cb => {
                    cb.checked = selectAll.checked;
                });
            });
        }
    });
</script>