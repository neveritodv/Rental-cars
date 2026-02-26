@props(['agent'])

<div class="dropdown">
    <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="ti ti-dots-vertical"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end p-2">
        {{-- Voir détails - contrôlé par permission VIEW --}}
        @can('agents.general.view')
        <li>
            <a class="dropdown-item rounded-1" href="{{ route('backoffice.agents.show', $agent) }}">
                <i class="ti ti-eye me-2"></i>
                Voir détails
            </a>
        </li>
        @endcan

        {{-- Modifier - contrôlé par permission EDIT --}}
        @can('agents.general.edit')
        <li>
            <a class="dropdown-item rounded-1" href="{{ route('backoffice.agents.edit', $agent) }}">
                <i class="ti ti-edit me-2"></i>
                Modifier
            </a>
        </li>
        @endcan

        {{-- Supprimer - contrôlé par permission DELETE --}}
        @can('agents.general.delete')
        <li>
            <hr class="dropdown-divider">
        </li>
        <li>
            {{-- 100% WORKING VERSION - TESTED --}}
            <a class="dropdown-item rounded-1 text-danger" 
               href="#"
               onclick="event.preventDefault(); event.stopPropagation(); document.getElementById('deleteAgentForm').action = '{{ route('backoffice.agents.destroy', $agent->id) }}'; document.getElementById('deleteAgentName').innerText = '{{ $agent->full_name }}'; new bootstrap.Modal(document.getElementById('delete_agent')).show(); return false;">
                <i class="ti ti-trash me-2"></i>
                Supprimer
            </a>
        </li>
        @endcan
    </ul>
</div>