@extends('layout.mainlayout_admin')

@section('content')
@php
function getItemName($item) {
    if (isset($item->name) && $item->name) {
        return $item->name;
    }
    if (isset($item->first_name) && $item->first_name) {
        return trim($item->first_name . ' ' . ($item->last_name ?? ''));
    }
    if (isset($item->title) && $item->title) {
        return $item->title;
    }
    if (isset($item->contract_number) && $item->contract_number) {
        return 'le contrat #' . $item->contract_number;
    }
    if (isset($item->registration_number) && $item->registration_number) {
        return 'le véhicule ' . $item->registration_number;
    }
    if (isset($item->invoice_number) && $item->invoice_number) {
        return 'la facture #' . $item->invoice_number;
    }
    if (isset($item->plan_name) && $item->plan_name) {
        return "l'abonnement " . $item->plan_name;
    }
    if (isset($item->description) && $item->description) {
        return $item->description;
    }
    return "l'élément #" . $item->id;
}
@endphp

<style>
    .trash-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 10px;
        margin-bottom: 20px;
    }
    
    .trash-tabs {
        background: white;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid #dee2e6;
    }
    
    .trash-tabs .nav-tabs {
        border-bottom: 2px solid #dee2e6;
    }
    
    .trash-tabs .nav-link {
        color: #6c757d;
        font-weight: 500;
        border: none;
        padding: 10px 20px;
        margin-right: 5px;
        border-radius: 5px 5px 0 0;
    }
    
    .trash-tabs .nav-link:hover {
        background: #f8f9fa;
        color: #0d6efd;
    }
    
    .trash-tabs .nav-link.active {
        color: #0d6efd;
        background: white;
        border-bottom: 2px solid #0d6efd;
    }
    
    .trash-tabs .nav-link i {
        margin-right: 8px;
    }
    
    .tab-count {
        background: #e9ecef;
        color: #495057;
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 0.75rem;
        margin-left: 8px;
    }
    
    .nav-link.active .tab-count {
        background: #0d6efd;
        color: white;
    }
    
    .module-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        border: 1px solid #dee2e6;
    }
    
    .module-title {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f0f0f0;
    }
    
    .restore-btn {
        color: #28a745;
        cursor: pointer;
        margin-right: 10px;
        font-size: 18px;
        display: inline-block;
    }
    
    .restore-btn:hover {
        transform: scale(1.1);
    }
    
    .delete-btn {
        color: #dc3545;
        cursor: pointer;
        font-size: 18px;
        display: inline-block;
    }
    
    .delete-btn:hover {
        transform: scale(1.1);
    }
    
    .badge-count {
        background: #dc3545;
        color: white;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        margin-left: 10px;
    }
    
    .empty-state {
        padding: 60px 20px;
        text-align: center;
    }
    
    .empty-state i {
        font-size: 64px;
        color: #dee2e6;
        margin-bottom: 20px;
    }
    
    .empty-state h4 {
        color: #6c757d;
        margin-bottom: 10px;
    }
    
    .empty-state p {
        color: #adb5bd;
    }
</style>

<div class="page-wrapper">
    <div class="content">
        <!-- Trash Header -->
        <div class="trash-header d-flex justify-content-between align-items-center">
            <div>
                <h2 class="text-white mb-2">
                    <i class="ti ti-trash me-2"></i>Corbeille
                </h2>
                <p class="text-white-50 mb-0">{{ $total }} élément(s) dans la corbeille</p>
            </div>
            <div class="d-flex gap-2">
                @if($total > 0)
                <button class="btn btn-danger" onclick="confirmEmptyAll()">
                    <i class="ti ti-trash-x me-2"></i>Vider toute la corbeille
                </button>
                @endif
                <a href="{{ route('backoffice.dashboard') }}" class="btn btn-light">
                    <i class="ti ti-arrow-left me-2"></i>Retour
                </a>
            </div>
        </div>

        @if($total > 0)
        <!-- Tabs Navigation -->
        <div class="trash-tabs">
            <ul class="nav nav-tabs" id="trashTabs" role="tablist">
                @foreach($data as $key => $module)
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $loop->first ? 'active' : '' }}" 
                            id="{{ $key }}-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#{{ str_replace('-', '_', $key) }}" 
                            type="button" 
                            role="tab">
                        <i class="{{ $module['icon'] }}"></i>
                        {{ $module['name'] }}
                        <span class="tab-count">{{ $module['count'] }}</span>
                    </button>
                </li>
                @endforeach
            </ul>
        </div>

        <!-- Tab Content -->
        <div class="tab-content">
            @foreach($data as $key => $module)
            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" 
                 id="{{ str_replace('-', '_', $key) }}" 
                 role="tabpanel">
                
                <div class="module-card">
                    <div class="module-title d-flex justify-content-between align-items-center">
                        <div>
                            <i class="{{ $module['icon'] }} me-2 text-danger"></i>
                            {{ $module['name'] }} supprimés
                        </div>
                        <div>
                            <button class="btn btn-sm btn-success me-2" onclick="confirmRestoreAll('{{ $key }}')">
                                <i class="ti ti-history me-1"></i>Tout restaurer
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="confirmDeleteAll('{{ $key }}')">
                                <i class="ti ti-trash-x me-1"></i>Tout supprimer
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nom / Titre</th>
                                    <th>Supprimé le</th>
                                    <th width="150">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($module['items'] as $item)
                                <tr>
                                    <td>#{{ $item->id }}</td>
                                    <td>
                                        @if(isset($item->name) && $item->name)
                                            {{ $item->name }}
                                        @elseif(isset($item->first_name) && $item->first_name)
                                            {{ $item->first_name }} {{ $item->last_name ?? '' }}
                                        @elseif(isset($item->title) && $item->title)
                                            {{ $item->title }}
                                        @elseif(isset($item->contract_number) && $item->contract_number)
                                            {{ $item->contract_number }}
                                        @elseif(isset($item->registration_number) && $item->registration_number)
                                            {{ $item->registration_number }}
                                        @elseif(isset($item->invoice_number) && $item->invoice_number)
                                            {{ $item->invoice_number }}
                                        @elseif(isset($item->plan_name) && $item->plan_name)
                                            {{ $item->plan_name }}
                                        @elseif(isset($item->description) && $item->description)
                                            {{ $item->description }}
                                        @else
                                            Élément #{{ $item->id }}
                                        @endif
                                    </td>
                                    <td>{{ $item->deleted_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <span class="restore-btn" 
                                              data-bs-toggle="modal" 
                                              data-bs-target="#restoreModal"
                                              data-restore-action="{{ route('backoffice.trash.restore', [$key, $item->id]) }}"
                                              data-restore-details="{{ getItemName($item) }}">
                                            <i class="ti ti-history" title="Restaurer"></i>
                                        </span>
                                        <span class="delete-btn" 
                                              data-bs-toggle="modal" 
                                              data-bs-target="#deleteModal"
                                              data-delete-action="{{ route('backoffice.trash.force-delete', [$key, $item->id]) }}"
                                              data-delete-details="{{ getItemName($item) }}">
                                            <i class="ti ti-trash-x" title="Supprimer définitivement"></i>
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <!-- Empty State -->
        <div class="card">
            <div class="empty-state">
                <i class="ti ti-trash-off"></i>
                <h4>La corbeille est vide</h4>
                <p>Aucun élément supprimé pour le moment.</p>
                <!-- <a href="{{ route('backoffice.dashboard') }}" class="btn btn-primary mt-3">
                    <i class="ti ti-arrow-left me-2"></i>Retour au tableau de bord
                </a> -->
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Restore Modal -->
<div class="modal fade" id="restoreModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center">
                <span class="avatar avatar-lg bg-transparent-success rounded-circle text-success mb-3">
                    <i class="ti ti-history fs-26"></i>
                </span>
                <h4 class="mb-1">Restaurer l'élément</h4>
                <p class="mb-3" id="restoreText">Êtes-vous sûr de vouloir restaurer cet élément ?</p>
                
                <form method="POST" action="" id="restoreForm">
                    @csrf
                    @method('PATCH')
                    
                    <div class="d-flex justify-content-center">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success">Oui, restaurer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center">
                <span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
                    <i class="ti ti-trash-x fs-26"></i>
                </span>
                <h4 class="mb-1">Supprimer définitivement</h4>
                <p class="mb-3" id="deleteText">Cette action est irréversible. Êtes-vous sûr ?</p>
                
                <form method="POST" action="" id="deleteForm">
                    @csrf
                    @method('DELETE')
                    
                    <div class="d-flex justify-content-center">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-danger">Oui, supprimer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Empty All Form -->
<form id="emptyAllForm" method="POST" action="{{ route('backoffice.trash.empty-all') }}" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<!-- Include SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Restore Modal
    const restoreModal = document.getElementById('restoreModal');
    if (restoreModal) {
        restoreModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            
            if (button) {
                const action = button.getAttribute('data-restore-action');
                const details = button.getAttribute('data-restore-details') || 'cet élément';
                
                const form = document.getElementById('restoreForm');
                const text = document.getElementById('restoreText');
                
                if (action && form) {
                    form.action = action;
                }
                
                if (text && details) {
                    text.innerHTML = 'Êtes-vous sûr de vouloir restaurer <strong>' + details + '</strong> ?';
                }
            }
        });
    }
    
    // Delete Modal
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            
            if (button) {
                const action = button.getAttribute('data-delete-action');
                const details = button.getAttribute('data-delete-details') || 'cet élément';
                
                const form = document.getElementById('deleteForm');
                const text = document.getElementById('deleteText');
                
                if (action && form) {
                    form.action = action;
                }
                
                if (text && details) {
                    text.innerHTML = 'Cette action est irréversible. Êtes-vous sûr de vouloir supprimer définitivement <strong>' + details + '</strong> ?';
                }
            }
        });
    }
});

// SweetAlert confirmations for bulk actions
function confirmRestoreAll(module) {
    Swal.fire({
        title: 'Restaurer tous les éléments ?',
        text: 'Voulez-vous restaurer tous les éléments de ce module ?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Oui, tout restaurer',
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/backoffice/trash/${module}/restore-all`;
            form.innerHTML = '@csrf @method("PATCH")';
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function confirmDeleteAll(module) {
    Swal.fire({
        title: 'Supprimer définitivement ?',
        text: 'Cette action est irréversible. Voulez-vous supprimer définitivement tous les éléments de ce module ?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Oui, tout supprimer',
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/backoffice/trash/${module}/force-delete-all`;
            form.innerHTML = '@csrf @method("DELETE")';
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function confirmEmptyAll() {
    Swal.fire({
        title: 'Vider toute la corbeille ?',
        text: 'Cette action est irréversible. Tous les éléments seront définitivement supprimés.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Oui, tout vider',
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('emptyAllForm').submit();
        }
    });
}
</script>
@endsection