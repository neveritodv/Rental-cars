<head>
    @section('content')
<script>
// Override the native alert function to block DataTables warnings
(function() {
    // Store the original alert
    var originalAlert = window.alert;
    
    // Replace with filtered version
    window.alert = function(message) {
        // Check if this is a DataTables warning
        if (message && (message.includes('DataTables') || message.includes('datatables'))) {
            console.log('DataTables warning blocked:', message);
            return; // Block the alert
        }
        // Allow other alerts through
        originalAlert(message);
    };
    
    // Also set DataTables error mode if available
    if (window.$ && $.fn && $.fn.dataTable) {
        $.fn.dataTable.ext.errMode = 'none';
    }
})();
</script>
</head>
<?php $page = 'vehicle-credits'; ?>

@extends('layout.mainlayout_admin')

@section('content')
<div class="page-wrapper">
    <div class="content me-4">

        @include('Backoffice.vehicle-credits.partials._breadcrumbs', ['vehicle' => $vehicle ?? null])

        <form method="GET" id="filterForm" action="{{ request()->url() }}">
            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
                <div class="d-flex align-items-center flex-wrap row-gap-3">
                    <div class="dropdown me-2">
                        <a href="#" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                            <i class="ti ti-filter me-1"></i> Trier : 
                            @if(request('sort') == 'oldest') Plus anciens
                            @elseif(request('sort') == 'amount_asc') Montant ↑
                            @elseif(request('sort') == 'amount_desc') Montant ↓
                            @elseif(request('sort') == 'remaining_asc') Restant ↑
                            @elseif(request('sort') == 'remaining_desc') Restant ↓
                            @else Plus récents @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end p-2">
                            <li><a class="dropdown-item" href="{{ route('backoffice.vehicle-credits.index', request()->except('sort')) }}">Plus récents</a></li>
                            <li><a class="dropdown-item" href="{{ route('backoffice.vehicle-credits.index', array_merge(request()->except('sort'), ['sort'=>'oldest'])) }}">Plus anciens</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('backoffice.vehicle-credits.index', array_merge(request()->except('sort'), ['sort'=>'amount_desc'])) }}">Montant (plus élevé)</a></li>
                            <li><a class="dropdown-item" href="{{ route('backoffice.vehicle-credits.index', array_merge(request()->except('sort'), ['sort'=>'amount_asc'])) }}">Montant (moins élevé)</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('backoffice.vehicle-credits.index', array_merge(request()->except('sort'), ['sort'=>'remaining_desc'])) }}">Restant (plus élevé)</a></li>
                            <li><a class="dropdown-item" href="{{ route('backoffice.vehicle-credits.index', array_merge(request()->except('sort'), ['sort'=>'remaining_asc'])) }}">Restant (moins élevé)</a></li>
                        </ul>
                    </div>
                    <div>
                        <a href="#filtercollapse" class="filtercollapse coloumn d-inline-flex align-items-center" data-bs-toggle="collapse">
                            <i class="ti ti-filter me-1"></i> Filtres
                        </a>
                    </div>
                </div>

                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                    <div class="top-search me-2">
                        <div class="top-search-group position-relative">
                            <span class="input-icon"><i class="ti ti-search"></i></span>
                            <input type="text" name="search" id="searchInput" value="{{ request('search') }}" class="form-control" placeholder="Rechercher un crédit...">
                            @if(request('search'))
                                <button type="button" class="btn btn-link position-absolute" style="right: 5px; top: 50%; transform: translateY(-50%); padding: 0; color: #6c757d; z-index: 10;" onclick="clearSearch()">
                                    <i class="ti ti-x"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="mb-0">
                        {{-- Bouton pour ouvrir le modal de création - contrôlé par permission CREATE --}}
                        @can('vehicle-credits.general.create')
                        <button type="button" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_credit">
                            <i class="ti ti-plus me-2"></i>Ajouter un crédit
                        </button>
                        @endcan
                    </div>
                </div>
            </div>

            <div class="collapse @if(request()->has('status') || request()->has('creditor') || request()->has('amount_min') || request()->has('amount_max') || request()->has('date_from') || request()->has('date_to')) show @endif" id="filtercollapse">
                <div class="filterbox p-3 mb-3 bg-light-100 rounded">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Statut</label>
                            <select name="status" form="filterForm" class="form-select" onchange="this.form.submit()">
                                <option value="">Tous</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Terminé</option>
                                <option value="defaulted" {{ request('status') == 'defaulted' ? 'selected' : '' }}>En défaut</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Créancier</label>
                            <input type="text" form="filterForm" name="creditor" value="{{ request('creditor') }}" class="form-control" placeholder="Nom du créancier" onchange="this.form.submit()">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Montant min (DH)</label>
                            <input type="number" form="filterForm" name="amount_min" value="{{ request('amount_min') }}" class="form-control" placeholder="0.00" step="0.01" onchange="this.form.submit()">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Montant max (DH)</label>
                            <input type="number" form="filterForm" name="amount_max" value="{{ request('amount_max') }}" class="form-control" placeholder="9999.99" step="0.01" onchange="this.form.submit()">
                        </div>
                        <div class="col-md-3 mt-2">
                            <label class="form-label fw-medium">Date début</label>
                            <input type="date" form="filterForm" name="date_from" value="{{ request('date_from') }}" class="form-control" onchange="this.form.submit()">
                        </div>
                        <div class="col-md-3 mt-2">
                            <label class="form-label fw-medium">Date fin</label>
                            <input type="date" form="filterForm" name="date_to" value="{{ request('date_to') }}" class="form-control" onchange="this.form.submit()">
                        </div>
                        <div class="col-md-3 mt-2 d-flex align-items-end">
                            <a href="{{ route('backoffice.vehicle-credits.index') }}" class="btn btn-sm btn-outline-danger w-100">
                                <i class="ti ti-x me-1"></i>Tout effacer
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="custom-datatable-filter table-responsive">
            @include('Backoffice.vehicle-credits.partials._table', ['credits' => $credits, 'permissions' => $permissions])
        </div>

        @if(isset($credits) && $credits->total() > 0)
        <div class="table-footer">
            <div class="d-flex justify-content-end">{{ $credits->withQueryString()->links() }}</div>
        </div>
        @endif

    </div>

    <div class="footer d-sm-flex align-items-center justify-content-between bg-white p-3">
        <p class="mb-0"><a href="javascript:void(0);">Privacy Policy</a><a href="javascript:void(0);" class="ms-4">Terms of Use</a></p>
        <p>&copy; 2025 Dreamsrent, Made with <span class="text-danger">❤</span> by <a href="javascript:void(0);" class="text-secondary">Dreams</a></p>
    </div>
</div>

{{-- Create Credit Modal --}}
@can('vehicle-credits.general.create')
<div class="modal fade" id="add_credit">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form action="{{ route('backoffice.vehicle-credits.store') }}" 
                  method="POST" 
                  class="needs-validation" 
                  novalidate
                  enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="ti ti-credit-card me-2"></i>
                        Ajouter un crédit
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Vehicle Selection -->
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">
                                    Véhicule <span class="text-danger">*</span>
                                </label>
                                <select name="vehicle_id" 
                                        id="modal_vehicle_select"
                                        class="form-select" 
                                        required>
                                    <option value="">Sélectionner un véhicule</option>
                                    @foreach($vehicles as $v)
                                        <option value="{{ $v->id }}">
                                            {{ $v->registration_number }} 
                                            @if($v->brand || $v->model)
                                                - {{ $v->brand->name ?? '' }} {{ $v->model->name ?? '' }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Creditor Name -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    Créancier <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       name="creditor_name"
                                       class="form-control"
                                       maxlength="150"
                                       placeholder="Ex: Banque, Institution..."
                                       required>
                            </div>
                        </div>

                        <!-- Total Amount -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    Montant total <span class="text-danger">*</span> (MAD)
                                </label>
                                <div class="input-group">
                                    <input type="number"
                                           name="total_amount"
                                           class="form-control"
                                           required
                                           step="0.01"
                                           min="0"
                                           placeholder="0.00">
                                    <span class="input-group-text">DH</span>
                                </div>
                            </div>
                        </div>

                        <!-- Down Payment -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    Apport (MAD)
                                </label>
                                <div class="input-group">
                                    <input type="number"
                                           name="down_payment"
                                           class="form-control"
                                           step="0.01"
                                           min="0"
                                           value="0"
                                           placeholder="0.00">
                                    <span class="input-group-text">DH</span>
                                </div>
                            </div>
                        </div>

                        <!-- Monthly Payment -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    Mensualité <span class="text-danger">*</span> (MAD)
                                </label>
                                <div class="input-group">
                                    <input type="number"
                                           name="monthly_payment"
                                           class="form-control"
                                           required
                                           step="0.01"
                                           min="0"
                                           placeholder="0.00">
                                    <span class="input-group-text">DH</span>
                                </div>
                            </div>
                        </div>

                        <!-- Duration -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    Durée <span class="text-danger">*</span> (mois)
                                </label>
                                <input type="number"
                                       name="duration_months"
                                       class="form-control"
                                       required
                                       min="1"
                                       max="120"
                                       placeholder="Ex: 36">
                            </div>
                        </div>

                        <!-- Interest Rate -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    Taux d'intérêt (%)
                                </label>
                                <div class="input-group">
                                    <input type="number"
                                           name="interest_rate"
                                           class="form-control"
                                           step="0.01"
                                           min="0"
                                           max="100"
                                           value="0"
                                           placeholder="0.00">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Start Date -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    Date de début <span class="text-danger">*</span>
                                </label>
                                <input type="date"
                                       name="start_date"
                                       class="form-control"
                                       value="{{ date('Y-m-d') }}"
                                       required>
                            </div>
                        </div>

                        <!-- Contract File -->
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">
                                    Contrat (PDF)
                                </label>
                                <input type="file"
                                       name="contract_file"
                                       class="form-control"
                                       accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">Max 2MB, formats: PDF, JPG, PNG</small>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea name="notes"
                                          class="form-control"
                                          rows="4"
                                          placeholder="Informations complémentaires..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer le crédit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('filterForm');
    const input = document.getElementById('searchInput');
    if (!form || !input) return;
    let debounceTimer;
    input.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function () { form.submit(); }, 400);
    });

    // Bootstrap validation for modal form
    @can('vehicle-credits.general.create')
    const modalForms = document.querySelectorAll('#add_credit .needs-validation');
    Array.from(modalForms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Initialize select2 for modal if available
    if (typeof $.fn.select2 !== 'undefined' && document.getElementById('modal_vehicle_select')) {
        $('#modal_vehicle_select').select2({
            placeholder: 'Sélectionner un véhicule',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#add_credit')
        });
    }
    @endcan
});

function clearSearch() {
    const input = document.getElementById('searchInput');
    if (input) { input.value = ''; document.getElementById('filterForm').submit(); }
}
</script>

{{-- Include modals JavaScript and delete modal --}}
@include('Backoffice.vehicle-credits.partials._modals_js')
@include('Backoffice.vehicle-credits.partials._modal_delete')
@endsection