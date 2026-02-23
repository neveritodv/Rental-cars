{{-- =========================
DELETE CREDIT MODAL
========================= --}}
<div class="modal fade" id="delete_credit">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <form method="POST" action="#" id="deleteCreditForm">
                @csrf
                @method('DELETE')

                <div class="modal-body text-center">
                    <span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
                        <i class="ti ti-trash-x fs-26"></i>
                    </span>
                    <h4 class="mb-1">Supprimer le crédit</h4>
                    <p class="mb-3" id="deleteCreditText">Êtes-vous sûr de vouloir supprimer ce crédit ?</p>
                    <div class="d-flex justify-content-center">
                        <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">Annuler</a>
                        <button type="submit" class="btn btn-danger">Oui, supprimer</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>