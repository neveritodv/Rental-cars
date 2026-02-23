<div class="modal fade" id="deleteNotificationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center">
                <span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
                    <i class="ti ti-trash-x fs-26"></i>
                </span>
                <h4 class="mb-1">Supprimer la notification</h4>
                <p class="mb-3" id="deleteNotificationText">Êtes-vous sûr de vouloir supprimer cette notification ?</p>
                
                <form method="POST" action="" id="deleteNotificationForm">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteModal = document.getElementById('deleteNotificationModal');
    const deleteForm = document.getElementById('deleteNotificationForm');
    
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            
            if (button) {
                const notificationId = button.getAttribute('data-id');
                const notificationTitle = button.getAttribute('data-title') || 'cette notification';
                
                if (deleteForm) {
                    deleteForm.action = `/backoffice/notifications/${notificationId}`;
                }
                
                const text = document.getElementById('deleteNotificationText');
                if (text && notificationTitle) {
                    text.innerHTML = `Êtes-vous sûr de vouloir supprimer <strong>${notificationTitle}</strong> ?`;
                }
            }
        });
        
        if (deleteForm) {
            deleteForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                fetch(this.action, {
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
                        const modal = bootstrap.Modal.getInstance(deleteModal);
                        if (modal) modal.hide();
                        
                        const notificationId = this.action.split('/').pop();
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
                    alert('Erreur lors de la suppression');
                });
            });
        }
    }
});
</script>