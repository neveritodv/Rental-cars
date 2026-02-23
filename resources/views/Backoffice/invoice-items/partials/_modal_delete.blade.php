<div class="modal fade" id="delete_invoice_item" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center">
                <span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
                    <i class="ti ti-trash-x fs-26"></i>
                </span>
                <h4 class="mb-1">Supprimer la ligne</h4>
                <p class="mb-3" id="deleteInvoiceItemText">Êtes-vous sûr de vouloir supprimer cette ligne ?</p>
                
                <form method="POST" action="" id="deleteInvoiceItemForm">
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
    const deleteModal = document.getElementById('delete_invoice_item');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            
            if (button) {
                const action = button.getAttribute('data-delete-action');
                const details = button.getAttribute('data-delete-details') || 'cette ligne';
                
                const form = document.getElementById('deleteInvoiceItemForm');
                const text = document.getElementById('deleteInvoiceItemText');
                
                if (action && form) {
                    form.action = action;
                }
                
                if (text && details) {
                    text.innerHTML = 'Êtes-vous sûr de vouloir supprimer ' + details + ' ?';
                }
            }
        });
    }
});
</script>