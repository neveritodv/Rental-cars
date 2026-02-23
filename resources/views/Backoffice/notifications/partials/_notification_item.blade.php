<div class="notification-item p-3 border-bottom {{ !$notification->is_read ? 'unread' : '' }}" data-id="{{ $notification->id }}">
    <div class="d-flex align-items-start">
        <div class="notification-icon me-3" style="background-color: {{ $notification->color }}20;">
            <i class="{{ $notification->icon }}" style="color: {{ $notification->color }};"></i>
        </div>
        <div class="flex-grow-1">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <h6 class="mb-0">{{ $notification->title }}</h6>
                <small class="text-muted">{{ $notification->formatted_time }}</small>
            </div>
            <p class="mb-1">{{ $notification->message }}</p>
            @if($notification->link)
                <a href="{{ $notification->link }}" class="small text-primary">Voir les détails</a>
            @endif
            <div class="d-flex justify-content-end mt-2">
                @if(!$notification->is_read)
                    <button class="btn btn-sm btn-link text-primary mark-read me-2" data-id="{{ $notification->id }}">
                        <i class="ti ti-check me-1"></i>Marquer comme lu
                    </button>
                @endif
                @if(!$notification->is_archived)
                    <button class="btn btn-sm btn-link text-secondary archive-notification me-2" data-id="{{ $notification->id }}">
                        <i class="ti ti-archive me-1"></i>Archiver
                    </button>
                @endif
                <button class="btn btn-sm btn-link text-danger delete-notification" data-id="{{ $notification->id }}">
                    <i class="ti ti-trash me-1"></i>Supprimer
                </button>
            </div>
        </div>
    </div>
</div>