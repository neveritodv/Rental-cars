<?php $page = 'notifications-archived'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<style>
    .notification-item {
        transition: background-color 0.2s;
        opacity: 0.8;
    }
    .notification-item:hover {
        opacity: 1;
        background-color: #f8f9fa;
    }
    .avatar-placeholder {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        overflow: hidden;
    }
    .avatar-placeholder img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .action-buttons {
        display: flex;
        gap: 1rem;
        align-items: center;
    }
</style>

<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="ti ti-archive me-2"></i>
                            Archives des notifications
                        </h4>
                        <div class="action-buttons">
                            @if($notifications->total() > 0)
                                <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteAllArchivedModal">
                                    <i class="ti ti-trash me-1"></i>Tout supprimer
                                </button>
                            @endif
                            <a href="{{ route('backoffice.notifications.index') }}" class="btn btn-outline-primary btn-sm">
                                <i class="ti ti-arrow-left me-1"></i>Retour
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="notifications-list">
                            @forelse($notifications as $notification)
                                <div class="notification-item p-3 border-bottom" data-id="{{ $notification->id }}">
                                    <div class="d-flex align-items-start">
                                        <div class="avatar-placeholder me-2">
                                            <img src="{{ URL::asset('assets/place-holder.webp') }}" alt="Avatar">
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1">{{ $notification->title }}</h6>
                                                    <p class="mb-1 text-muted">{{ $notification->message }}</p>
                                                    @if($notification->link)
                                                        <a href="{{ $notification->link }}" class="small text-primary">Voir les détails <i class="ti ti-chevron-right ms-1"></i></a>
                                                    @endif
                                                </div>
                                                <small class="text-muted">{{ $notification->formatted_time }}</small>
                                            </div>
                                            <div class="d-flex mt-2">
                                                <button class="btn btn-sm btn-link text-danger p-0" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#deleteNotificationModal"
                                                        data-id="{{ $notification->id }}"
                                                        data-title="{{ $notification->title }}">
                                                    <i class="ti ti-trash me-1"></i>Supprimer
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5">
                                    <i class="ti ti-archive-off fs-48 text-muted mb-3"></i>
                                    <h5>Aucune archive</h5>
                                    <p class="text-muted">Les notifications que vous archivez apparaîtront ici</p>
                                </div>
                            @endforelse
                        </div>
                        
                        @if($notifications->total() > 0)
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <div class="text-muted">
                                    Affichage de {{ $notifications->firstItem() }} à {{ $notifications->lastItem() }} sur {{ $notifications->total() }} archives
                                </div>
                                <div>
                                    {{ $notifications->withQueryString()->links() }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Modals -->
@include('Backoffice.notifications.partials._modal_delete')
@include('Backoffice.notifications.partials._modal_delete_all_archived')

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Delete all archived
    document.getElementById('confirmDeleteAllArchived')?.addEventListener('click', function() {
        fetch('/backoffice/notifications/delete-all-archived', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $('#deleteAllArchivedModal').modal('hide');
                location.reload();
            } else {
                alert('Erreur: ' + (data.message || 'Erreur inconnue'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors de la suppression');
        });
    });
});
</script>
@endsection