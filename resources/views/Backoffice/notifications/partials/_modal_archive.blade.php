<div class="modal fade" id="archiveNotificationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center">
                <span class="avatar avatar-lg bg-transparent-warning rounded-circle text-warning mb-3">
                    <i class="ti ti-archive fs-26"></i>
                </span>
                <h4 class="mb-1">Archiver la notification</h4>
                <p class="mb-3" id="archiveNotificationText">Êtes-vous sûr de vouloir archiver cette notification ?</p>
                
                <form method="POST" action="" id="archiveNotificationForm">
                    @csrf
                    
                    <div class="d-flex justify-content-center">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-warning">Oui, archiver</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const archiveModal = document.getElementById('archiveNotificationModal');
    const archiveForm = document.getElementById('archiveNotificationForm');
    
    if (archiveModal) {
        archiveModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            
            if (button) {
                const notificationId = button.getAttribute('data-id');
                const notificationTitle = button.getAttribute('data-title') || 'cette notification';
                
                if (archiveForm) {
                    archiveForm.action = `/backoffice/notifications/${notificationId}/archive`;
                }
                
                const text = document.getElementById('archiveNotificationText');
                if (text && notificationTitle) {
                    text.innerHTML = `Êtes-vous sûr de vouloir archiver <strong>${notificationTitle}</strong> ?`;
                }
            }
        });
        
        if (archiveForm) {
            archiveForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                fetch(this.action, {
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
                        const modal = bootstrap.Modal.getInstance(archiveModal);
                        if (modal) modal.hide();
                        
                        const notificationId = this.action.split('/')[this.action.split('/').length - 2];
                        const notificationItem = document.querySelector(`.notification-item[data-id="${notificationId}"]`);
                        if (notificationItem) {
                            notificationItem.remove();
                        }
                    } else {
                        alert('Erreur: ' + (data.message || 'Erreur inconnue'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erreur lors de l\'archivage');
                });
            });
        }
    }
});
</script>