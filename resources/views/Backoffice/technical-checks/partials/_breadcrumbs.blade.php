@if(isset($isGlobalView) && $isGlobalView)
    <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
        <div class="my-auto mb-2">
            <h4 class="mb-1">Tous les contrôles techniques</h4>
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('backoffice.dashboard') }}">Accueil</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('backoffice.vehicles.index') }}">Véhicules</a></li>
                    <li class="breadcrumb-item active">Contrôles techniques</li>
                </ol>
            </nav>
        </div>
    </div>
@else
    <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
        <div class="my-auto mb-2">
            <h4 class="mb-1">Contrôles techniques</h4>
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('backoffice.dashboard') }}">Accueil</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('backoffice.vehicles.index') }}">Véhicules</a></li>
                    @if(isset($vehicle) && $vehicle)
                        <li class="breadcrumb-item"><a href="{{ route('backoffice.vehicles.show', $vehicle->id) }}">{{ $vehicle->registration_number ?? 'Véhicule' }}</a></li>
                    @endif
                    <li class="breadcrumb-item active">Contrôles techniques</li>
                </ol>
            </nav>
        </div>
    </div>
@endif