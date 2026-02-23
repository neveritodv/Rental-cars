<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h4 class="mb-1">Agents</h4>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('backoffice.dashboard') }}">Accueil</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('backoffice.agents.index') }}">Agents</a>
                </li>
                @if(request()->routeIs('backoffice.agents.show'))
                    <li class="breadcrumb-item active" aria-current="page">Détails</li>
                @elseif(request()->routeIs('backoffice.agents.edit'))
                    <li class="breadcrumb-item active" aria-current="page">Modification</li>
                @else
                    <li class="breadcrumb-item active" aria-current="page">Liste</li>
                @endif
            </ol>
        </nav>
    </div>
    
    @if(request()->routeIs('backoffice.agents.show'))
        <div class="mt-2 mt-md-0">
            <a href="{{ route('backoffice.agents.index') }}" class="btn btn-sm btn-white">
                <i class="ti ti-arrow-left me-1"></i>Retour à la liste
            </a>
        </div>
    @endif
</div>