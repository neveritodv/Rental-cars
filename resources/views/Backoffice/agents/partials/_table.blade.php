<head>
    <style>
        .table-responsive,
        .custom-datatable-filter,
        .dataTables_wrapper {
            overflow: visible !important;
        }
        
        /* Agent avatar styles */
        .agent-avatar-table {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            object-fit: cover;
        }
    </style>
</head>

<table class="table datatable">
    <thead class="thead-light">
        <tr>
            <th class="no-sort" width="50">
                <div class="form-check form-check-md">
                    <input class="form-check-input" type="checkbox" id="select-all">
                </div>
            </th>
            <th>Photo</th>
            <th>Agent</th>
            <th>Agence</th>
            <th>Contact</th>
            <th>Utilisateur lié</th>
            <th>Date d'ajout</th>
            <th width="80">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($agents as $agent)
        <tr>
            <td>
                <div class="form-check form-check-md">
                    <input class="form-check-input" type="checkbox">
                </div>
            </td>
            <td>
                @if($agent->avatar_url)
                    <img src="{{ $agent->avatar_url }}" 
                         alt="{{ $agent->full_name }}"
                         class="agent-avatar-table"
                         onerror="this.src='{{ asset('assets/place-holder.webp') }}';">
                @else
                    <img src="{{ asset('assets/place-holder.webp') }}" 
                         alt="{{ $agent->full_name }}"
                         class="agent-avatar-table">
                @endif
            </td>
            <td>
                <div class="d-flex flex-column">
                    <h6 class="fw-medium mb-0">
                        <a href="{{ route('backoffice.agents.show', $agent) }}" class="text-dark">
                            {{ $agent->full_name }}
                        </a>
                    </h6>
                    <small class="text-muted">ID: #{{ $agent->id }}</small>
                </div>
            </td>
            <td>
                <div class="d-flex align-items-center">
                    <i class="ti ti-building me-1 text-gray-4"></i>
                    <span>{{ $agent->agency->name ?? '—' }}</span>
                </div>
            </td>
            <td>
                <div class="d-flex flex-column">
                    @if($agent->email)
                        <a href="mailto:{{ $agent->email }}" class="text-primary small">
                            <i class="ti ti-mail me-1"></i>{{ Str::limit($agent->email, 20) }}
                        </a>
                    @endif
                    @if($agent->phone)
                        <a href="tel:{{ $agent->phone }}" class="text-success small mt-1">
                            <i class="ti ti-phone me-1"></i>{{ $agent->phone }}
                        </a>
                    @endif
                    @if(!$agent->email && !$agent->phone)
                        <span class="text-muted">—</span>
                    @endif
                </div>
            </td>
            <td>
                @if($agent->user)
                    <span class="badge bg-success-transparent">
                        <i class="ti ti-user-check me-1"></i>
                        {{ Str::limit($agent->user->name, 15) }}
                    </span>
                @else
                    <span class="badge bg-light-200">
                        <i class="ti ti-user-x me-1"></i>
                        Non lié
                    </span>
                @endif
            </td>
            <td>
                <div class="d-flex flex-column">
                    <small class="fw-medium">{{ $agent->created_at->format('d/m/Y') }}</small>
                    <small class="text-muted">{{ $agent->created_at->format('H:i') }}</small>
                </div>
            </td>
            <td>
                @include('backoffice.agents.partials._actions', ['agent' => $agent])
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="text-center py-5">
                <div class="text-center">
                    <h5 class="mb-2">Aucun agent trouvé</h5>
                </div>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>