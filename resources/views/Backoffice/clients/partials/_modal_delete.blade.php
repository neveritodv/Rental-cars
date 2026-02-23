{{-- Delete Client Modal --}}
<div class="modal fade" id="delete_client" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content text-center p-3">
            <span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
                <i class="ti ti-trash-x fs-26"></i>
            </span>
            <h4 class="mb-1">Supprimer le client</h4>
            <p class="mb-3">
                Êtes-vous sûr de vouloir supprimer le client
                <strong id="deleteClientName">—</strong> ?
            </p>
            <form id="deleteClientForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Oui, supprimer</button>
                </div>
            </form>
        </div>
    </div>
</div>  