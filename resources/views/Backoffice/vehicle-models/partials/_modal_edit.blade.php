<!-- Edit Model -->
<div class="modal fade" id="edit_model">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">

            <form id="editModelForm"
                  method="POST"
                  class="needs-validation"
                  novalidate>
                @csrf
                @method('PUT')

                <!-- Hidden ID field -->
                <input type="hidden" name="id" id="editModelId">

                <div class="modal-header">
                    <h5 class="mb-0">Modifier le modèle</h5>
                    <button type="button" class="btn-close custom-btn-close"
                            data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x fs-16"></i>
                    </button>
                </div>

                <div class="modal-body pb-1">

                    {{-- Model name --}}
                    <div class="mb-3">
                        <label class="form-label">
                            Modèle <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="name"
                               id="editModelName"
                               class="form-control"
                               placeholder="Ex: 4x4"
                               required>
                        <div class="invalid-feedback">
                            Le nom du modèle est obligatoire.
                        </div>
                    </div>

                    {{-- Brand --}}
                    <div class="mb-3">
                        <label class="form-label">
                            Marque <span class="text-danger">*</span>
                        </label>
                        <select name="vehicle_brand_id"
                                id="editModelBrand"
                                class="form-select"
                                required>
                            <option value="" disabled selected>Choisir une marque</option>
                            @foreach(($brands ?? []) as $brand)
                                <option value="{{ $brand->id }}">
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">
                            Veuillez sélectionner une marque.
                        </div>
                    </div>

                    {{-- Status (optional, based on your table) --}}
                    <div class="mb-3">
                        <label class="form-label">
                            Statut <span class="text-danger">*</span>
                        </label>
<select name="status" id="editModelStatus" class="form-select" required>
                            <option value="active" selected>Actif</option>
                            <option value="inactive">Inactif</option>
                            <option value="cancelled">Annulé</option>
                        </select>
                        <div class="invalid-feedback">
                            Veuillez sélectionner un statut.
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <div class="d-flex justify-content-center w-100">
                        <button type="button"
                                class="btn btn-light me-3"
                                data-bs-dismiss="modal">
                            Annuler
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Enregistrer
                        </button>
                    </div>
                </div>

            </form>

        </div>
    </div>
</div>
<script>
    // JavaScript to handle edit button click
document.querySelectorAll('.edit-model-btn').forEach(button => {
    button.addEventListener('click', function() {
        const modelId = this.getAttribute('data-id');
        const modelName = this.getAttribute('data-name');
        const brandId = this.getAttribute('data-brand-id');
        const status = this.getAttribute('data-status');
        
        // Set the form action with the correct route
        const form = document.getElementById('editModelForm');
        form.action = `/backoffice/vehicle-models/${modelId}`;
        
        // Populate form fields
        document.getElementById('editModelId').value = modelId;
        document.getElementById('editModelName').value = modelName;
        document.getElementById('editModelBrand').value = brandId;
        document.getElementById('editModelStatus').value = status;
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('edit_model'));
        modal.show();
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.edit-model-btn').forEach(button => {
        button.addEventListener('click', function () {

            const form = document.getElementById('editModelForm');

            // Set form action
            form.action = `/backoffice/vehicle-models/${this.dataset.id}`;

            // Populate fields
            document.getElementById('editModelId').value = this.dataset.id;
            document.getElementById('editModelName').value = this.dataset.name;
            document.getElementById('editModelBrand').value = this.dataset.brandId;
            document.getElementById('editModelStatus').value = this.dataset.status;

            // Show modal
            new bootstrap.Modal(document.getElementById('edit_model')).show();
        });
    });
});
</script>

<!-- /Edit Model -->