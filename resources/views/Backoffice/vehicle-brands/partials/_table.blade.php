<head>
    <style>
        .table-responsive,
        .dataTables_wrapper {
            overflow: visible !important;
        }
        .brand-avatar-img {
            width: 40px;
            height: 40px;
            min-width: 40px;
            min-height: 40px;
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            background-color: #fff;
            object-fit: contain;
            padding: 6px;
            display: inline-block;
            vertical-align: middle;
        }
    </style>
</head>

<table class="table datatable">
    <thead class="thead-light">
        <tr>
            <th class="no-sort">
                <div class="form-check form-check-md">
                    <input class="form-check-input" type="checkbox" id="select-all">
                </div>
            </th>
            <th>NAME</th>
            <th>TOTAL CARS</th>
            <th>STATUS</th>
            <th></th>
        </tr>
    </thead>

    <tbody>
        @forelse($brands as $brand)
            @php
                $logo = $brand->logo_url ?: asset('admin_assets/img/brands/toyota.svg');
                $carsCount = $brand->vehicles()->count();
            @endphp

            <tr>
                {{-- Checkbox --}}
                <td>
                    <div class="form-check form-check-md">
                        <input class="form-check-input" type="checkbox">
                    </div>
                </td>

                {{-- Brand with Logo --}}
                <td>
                    <div class="d-flex align-items-center file-name-icon">
                        <a href="javascript:void(0);" class="avatar avatar-lg border">
                            <img src="{{ $logo }}" class="img-fluid brand-avatar-img" alt="{{ $brand->name }}">
                        </a>
                        <div class="ms-2">
                            <h6 class="fw-medium mb-0">
                                <a href="javascript:void(0);">
                                    {{ $brand->name }}
                                </a>
                            </h6>
                        </div>
                    </div>
                </td>

                {{-- Total Cars --}}
                <td>{{ $carsCount }}</td>

                {{-- Status (static UI) --}}
                <td>
                    <span class="badge badge-success-transparent d-inline-flex align-items-center badge-sm">
                        <i class="ti ti-point-filled me-1"></i>Active
                    </span>
                </td>

                {{-- Actions (EDIT / DELETE) --}}
                <td>
                    @include('backoffice.vehicle-brands.partials._actions', [
                        'brand' => $brand,
                        'logo' => $logo,
                    ])
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center py-4">
                    <div class="text-muted">
                        <i class="ti ti-car-off fs-4 mb-2"></i>
                        <p class="mb-0">No brands found.</p>
                        @if(request('search'))
                            <!-- <a href="{{ route('backoffice.vehicle-brands.index') }}" class="btn btn-sm btn-primary mt-3">
                                Effacer les filtres
                            </a> -->
                        @endif
                    </div>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>