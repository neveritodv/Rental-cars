<table class="table datatable">
    <thead class="thead-light">
        <tr>
            <th class="no-sort">
                <div class="form-check form-check-md">
                    <input class="form-check-input" type="checkbox" id="select-all">
                </div>
            </th>
            <th>MODÈLE</th>
            <th>MARQUE</th>
            <th>STATUT</th>
            <th class="text-end no-sort">ACTIONS</th>
        </tr>
    </thead>

    <tbody>
        @forelse($models as $model)
        @php
        $brandName = $model->brand->name ?? '—';
        $brandLogo = $model->brand->logo_url ?? asset('admin_assets/img/brands/toyota.svg');
        @endphp

        <tr>
            {{-- Checkbox --}}
            <td>
                <div class="form-check form-check-md">
                    <input class="form-check-input" type="checkbox" value="{{ $model->id }}">
                </div>
            </td>

            {{-- Model --}}
            <td>
                <div class="d-flex align-items-center">
                    <div>
                        <h6 class="fw-medium mb-0">
                            <a href="javascript:void(0);">
                                {{ $model->name }}
                            </a>
                        </h6>
                    </div>
                </div>
            </td>

            {{-- Brand --}}
            <td>
                <div class="d-flex align-items-center">
                    <a href="javascript:void(0);" class="avatar avatar-lg border me-2">
                        <img src="{{ $brandLogo }}" class="img-fluid" alt="brand" style="object-fit: contain; width: 40px; height: 40px;">
                    </a>
                    <div>
                        <h6 class="fw-medium mb-0">
                            <a href="javascript:void(0);">
                                {{ $brandName }}
                            </a>
                        </h6>
                    </div>
                </div>
            </td>

            {{-- Status --}}
            <td>
                @if($model->is_active ?? true)
                    <span class="badge badge-success-transparent d-inline-flex align-items-center badge-sm">
                        <i class="ti ti-point-filled me-1"></i>Actif
                    </span>
                @else
                    <span class="badge badge-danger-transparent d-inline-flex align-items-center badge-sm">
                        <i class="ti ti-point-filled me-1"></i>Inactif
                    </span>
                @endif
            </td>

            {{-- Actions (EDIT / DELETE) --}}
            <td class="text-end position-static">
                @include('backoffice.vehicle-models.partials._actions', ['model' => $model])
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center py-4">
                <div class="text-muted">
                    <i class="ti ti-car-off fs-4 mb-2"></i>
                    <p class="mb-0">Aucun modèle trouvé.</p>
                    @if(request('search') || request('status'))
                        <!-- <a href="{{ route('backoffice.vehicle-models.index') }}" class="btn btn-sm btn-primary mt-3">
                            Effacer les filtres
                        </a> -->
                    @endif
                </div>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>