<head>
    <style>
        .table-responsive,
        .custom-datatable-filter,
        .dataTables_wrapper {
            overflow: visible !important;
        }
        
        .client-avatar-table {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            object-fit: cover;
        }

        .avatar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            text-decoration: none;
            background: #f8f9fa;
        }

        .avatar-md {
            width: 42px;
            height: 42px;
        }

        .avatar-title {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            font-size: 16px;
            font-weight: 600;
            color: #495057;
            background: #f8f9fa;
            border-radius: 12px;
        }
        
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
        }
        .btn-icon:hover {
            background: #f8f9fa;
            border-color: #dee2e6;
            color: #0d6efd;
        }
        .form-check {
            display: flex;
            justify-content: center;
            margin: 0;
            padding: 0;
        }
    </style>
</head>

<table class="table datatable align-middle">
    <thead class="thead-light">
        <tr>
            {{-- Case à cocher - visible seulement si permission DELETE --}}
            @can('clients.general.delete')
            <th class="no-sort" width="50">
                <div class="form-check form-check-md">
                    <input class="form-check-input" type="checkbox" id="select-all">
                </div>
            </th>
            @endcan
            <th>Photo</th>
            <th>Client</th>
            <th>Agence</th>
            <th>Contact</th>
            <th>Permis</th>
            <th>Statut</th>
            <th>Date d'ajout</th>
            {{-- Colonne Actions - visible seulement si au moins une permission d'action --}}
            @canany(['clients.general.view', 'clients.general.edit', 'clients.general.delete'])
            <th width="80">Actions</th>
            @endcanany
        </tr>
    </thead>
    <tbody>
        @forelse($clients as $client)
            <tr>
                {{-- Case à cocher - visible seulement si permission DELETE --}}
                @can('clients.general.delete')
                <td>
                    <div class="form-check form-check-md">
                        <input class="form-check-input client-checkbox" type="checkbox" value="{{ $client->id }}">
                    </div>
                </td>
                @endcan
                
                <td>
                    <div class="position-relative" style="width:42px;height:42px;">
                        @if ($client->hasAvatar())
                            <img src="{{ $client->avatar_url }}" alt="{{ $client->full_name }}"
                                class="client-avatar-table"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        @else
                            <img src="" style="display:none;" alt="{{ $client->full_name }}"
                                class="client-avatar-table">
                        @endif

                        <div class="avatar avatar-md border position-absolute top-0 start-0"
                            @if ($client->hasAvatar()) style="display: none;" @endif>
                            <span class="avatar-title">
                                {{ strtoupper(substr($client->first_name, 0, 1) . substr($client->last_name, 0, 1)) }}
                            </span>
                        </div>
                    </div>
                </td>

                <td>
                    <div class="d-flex flex-column">
                        <h6 class="fw-medium mb-0">
                            {{-- Lien vers show - contrôlé par permission VIEW --}}
                            @can('clients.general.view')
                                <a href="{{ route('backoffice.clients.show', $client) }}" class="text-dark">
                                    {{ $client->full_name }}
                                </a>
                            @else
                                <span class="text-dark">{{ $client->full_name }}</span>
                            @endcan
                        </h6>
                        <small class="text-muted">ID: #{{ $client->id }}</small>
                    </div>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <i class="ti ti-building me-1 text-gray-4"></i>
                        <span>{{ $client->agency->name ?? '—' }}</span>
                    </div>
                </td>
                <td>
                    <div class="d-flex flex-column">
                        @if ($client->email)
                            <a href="mailto:{{ $client->email }}" class="text-primary small">
                                <i class="ti ti-mail me-1"></i>{{ Str::limit($client->email, 20) }}
                            </a>
                        @endif
                        @if ($client->phone)
                            <a href="tel:{{ $client->phone }}" class="text-success small mt-1">
                                <i class="ti ti-phone me-1"></i>{{ $client->phone }}
                            </a>
                        @endif
                    </div>
                </td>
                <td>
                    @if ($client->driving_license_number)
                        <span class="badge bg-info-transparent">
                            <i class="ti ti-id me-1"></i>
                            {{ Str::limit($client->driving_license_number, 10) }}
                        </span>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                <td>
                    @if ($client->status == 'active')
                        <span class="badge bg-success-transparent">
                            <i class="ti ti-point-filled text-success me-1"></i>
                            Actif
                        </span>
                    @elseif($client->status == 'inactive')
                        <span class="badge bg-danger-transparent">
                            <i class="ti ti-point-filled text-danger me-1"></i>
                            Inactif
                        </span>
                    @else
                        <span class="badge bg-dark-transparent">
                            <i class="ti ti-point-filled text-dark me-1"></i>
                            Blacklisté
                        </span>
                    @endif
                </td>
                <td>
                    <div class="d-flex flex-column">
                        <small class="fw-medium">{{ $client->created_at->format('d/m/Y') }}</small>
                        <small class="text-muted">{{ $client->created_at->format('H:i') }}</small>
                    </div>
                </td>
                
                {{-- Actions - visible seulement si au moins une permission d'action --}}
                @canany(['clients.general.view', 'clients.general.edit', 'clients.general.delete'])
                <td>
                    @include('backoffice.clients.partials._actions', ['client' => $client])
                </td>
                @endcanany
            </tr>
        @empty
            <tr>
                @can('clients.general.delete')
                <td></td>
                @endcan
                <td colspan="{{ (auth()->user()->can('clients.general.delete') ? 8 : 7) }}" class="text-center py-5">
                    <div class="text-center">
                        <i class="ti ti-users-off fs-48 text-gray-4 mb-3"></i>
                        <h5 class="mb-2">Aucun client trouvé</h5>
                        @can('clients.general.create')
                            <p class="text-muted mb-3">Commencez par ajouter un nouveau client</p>
                            <a href="{{ route('backoffice.clients.create') }}" class="btn btn-primary">
                                <i class="ti ti-plus me-2"></i>
                                Ajouter un client
                            </a>
                        @endcan
                    </div>
                </td>
                @canany(['clients.general.view', 'clients.general.edit', 'clients.general.delete'])
                <td></td>
                @endcanany
            </tr>
        @endforelse
    </tbody>
</table>

{{-- Script pour "Select All" - seulement si permission DELETE --}}
@can('clients.general.delete')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                let checkboxes = document.querySelectorAll('.client-checkbox');
                checkboxes.forEach(cb => cb.checked = this.checked);
            });
        }
    });
</script>
@endcan