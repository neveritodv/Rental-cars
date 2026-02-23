<?php $page = 'notifications'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<style>
    .notification-item {
        transition: background-color 0.2s;
        border-left: 4px solid transparent;
    }
    .notification-item.unread {
        background-color: #f0f7ff;
        border-left-color: #0d6efd;
    }
    .notification-item:hover {
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
    .filter-tab {
        border-bottom: 2px solid transparent;
        padding-bottom: 0.5rem;
        font-weight: 500;
        cursor: pointer;
    }
    .filter-tab.active {
        color: #0d6efd;
        border-bottom-color: #0d6efd;
    }
    .action-buttons {
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
    }
</style>

<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="ti ti-bell me-2"></i>
                            Notifications
                        </h4>
                        <div class="action-buttons">
                            @if($notifications->total() > 0)
                                <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteAllModal">
                                    <i class="ti ti-trash me-1"></i>Tout supprimer
                                </button>
                            @endif
                            <button class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#archiveAllModal">
                                <i class="ti ti-archive me-1"></i>Tout archiver
                            </button>
                            <a href="{{ route('backoffice.notifications.archived') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="ti ti-archive me-1"></i>Archives
                            </a>
                            <button class="btn btn-outline-primary btn-sm" id="markAllRead">
                                <i class="ti ti-check me-1"></i>Tout marquer comme lu
                            </button>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <!-- Filters -->
                        <div class="d-flex align-items-center mb-4">
                            <div class="d-flex gap-4">
                                <a href="{{ route('backoffice.notifications.index') }}" class="filter-tab {{ !request('filter') ? 'active' : '' }}">Toutes</a>
                                <a href="{{ route('backoffice.notifications.index', ['filter' => 'unread']) }}" class="filter-tab {{ request('filter') == 'unread' ? 'active' : '' }}">Non lues</a>
                                <a href="{{ route('backoffice.notifications.index', ['filter' => 'read']) }}" class="filter-tab {{ request('filter') == 'read' ? 'active' : '' }}">Lues</a>
                            </div>
                        </div>
                        
                        <!-- Notifications List -->
                        <div class="notifications-list">
                            @forelse($notifications as $notification)
                                <div class="notification-item p-3 border-bottom {{ !$notification->is_read ? 'unread' : '' }}" data-id="{{ $notification->id }}">
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
                                            <div class="d-flex gap-3 mt-2">
                                                @if(!$notification->is_read)
                                                    <button class="btn btn-sm btn-link text-primary p-0 mark-read" data-id="{{ $notification->id }}">
                                                        <i class="ti ti-check me-1"></i>Marquer comme lu
                                                    </button>
                                                @endif
                                                @if(!$notification->is_archived)
                                                    <button class="btn btn-sm btn-link text-warning p-0" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#archiveNotificationModal"
                                                            data-id="{{ $notification->id }}"
                                                            data-title="{{ $notification->title }}">
                                                        <i class="ti ti-archive me-1"></i>Archiver
                                                    </button>
                                                @endif
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
                                    <i class="ti ti-bell-off fs-48 text-muted mb-3"></i>
                                    <h5>Aucune notification</h5>
                                    <p class="text-muted">Vous n'avez aucune notification pour le moment</p>
                                </div>
                            @endforelse
                        </div>
                        
                        <!-- Pagination -->
                        @if($notifications->total() > 0)
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <div class="text-muted">
                                    Affichage de {{ $notifications->firstItem() }} à {{ $notifications->lastItem() }} sur {{ $notifications->total() }} notifications
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
@include('Backoffice.notifications.partials._modal_archive')
@include('Backoffice.notifications.partials._modal_archive_all')
@include('Backoffice.notifications.partials._modal_delete_all')

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mark as read
    document.querySelectorAll('.mark-read').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const item = this.closest('.notification-item');
            
            fetch(`/backoffice/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    item.classList.remove('unread');
                    this.remove();
                }
            });
        });
    });
    
    // Mark all as read
    document.getElementById('markAllRead')?.addEventListener('click', function() {
        fetch('{{ route("backoffice.notifications.mark-all-read") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(() => {
            location.reload();
        });
    });
    
    // Archive all
    document.getElementById('confirmArchiveAll')?.addEventListener('click', function() {
        fetch('{{ route("backoffice.notifications.clear-all") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(() => {
            $('#archiveAllModal').modal('hide');
            location.reload();
        });
    });
    
    // Delete all
    document.getElementById('confirmDeleteAll')?.addEventListener('click', function() {
        fetch('{{ route("backoffice.notifications.delete-all") }}', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $('#deleteAllModal').modal('hide');
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