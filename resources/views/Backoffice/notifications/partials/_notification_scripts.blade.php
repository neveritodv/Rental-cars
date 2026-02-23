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
                    updateNotificationBadge();
                }
            });
        });
    });
    
    // Archive notification
    document.querySelectorAll('.archive-notification').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const item = this.closest('.notification-item');
            
            fetch(`/backoffice/notifications/${id}/archive`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    item.remove();
                    updateNotificationBadge();
                    
                    // Check if empty
                    if (document.querySelectorAll('.notification-item').length === 0) {
                        location.reload();
                    }
                }
            });
        });
    });
    
    // Delete notification
    document.querySelectorAll('.delete-notification').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            if (confirm('Supprimer définitivement cette notification ?')) {
                fetch(`/backoffice/notifications/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.closest('.notification-item').remove();
                        updateNotificationBadge();
                        
                        // Check if empty
                        if (document.querySelectorAll('.notification-item').length === 0) {
                            location.reload();
                        }
                    }
                });
            }
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
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    });
    
    // Clear all (archive all)
    document.getElementById('clearAll')?.addEventListener('click', function() {
        if (confirm('Archiver toutes les notifications ?')) {
            fetch('{{ route("backoffice.notifications.clear-all") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }
    });
    
    // Update notification badge in header
    function updateNotificationBadge() {
        fetch('/backoffice/notifications/unread-count')
            .then(response => response.json())
            .then(data => {
                const badge = document.querySelector('.notification_item .badge');
                if (badge) {
                    if (data.unread > 0) {
                        badge.textContent = data.unread;
                        badge.style.display = 'inline';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            });
    }
});
</script>