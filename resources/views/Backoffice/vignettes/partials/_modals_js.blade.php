<script>
document.addEventListener('DOMContentLoaded', function() {

    /* ======================
     | BOOTSTRAP VALIDATION
     ====================== */
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    /* ======================
     | EDIT VIGNETTE MODAL
     ====================== */
    const editModal = document.getElementById('edit_vignette');

    const setVal = (id, val) => {
        const el = document.getElementById(id);
        if (el !== null) el.value = val ?? '';
    };

    if (editModal) {
        editModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;

            const action = button ? button.getAttribute('data-edit-action') : null;

            const data = {
                date: button?.getAttribute('data-vignette-date') || '',
                amount: button?.getAttribute('data-vignette-amount') || '',
                year: button?.getAttribute('data-vignette-year') || '',
                notes: button?.getAttribute('data-vignette-notes') || '',
            };

            const form = document.getElementById('editVignetteForm');
            if (action && form) form.action = action;

            setVal('editVignetteDate', data.date);
            setVal('editVignetteAmount', data.amount);
            setVal('editVignetteYear', data.year);
            setVal('editVignetteNotes', data.notes);

            // reset validation
            if (form) form.classList.remove('was-validated');
        });

        editModal.addEventListener('hidden.bs.modal', function() {
            const form = document.getElementById('editVignetteForm');
            if (form) form.classList.remove('was-validated');
        });
    }

    /* ======================
     | DELETE VIGNETTE MODAL
     ====================== */
    const deleteModal = document.getElementById('delete_vignette');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;

            const action = button ? button.getAttribute('data-delete-action') : null;
            const details = button ? button.getAttribute('data-vignette-details') : '—';

            const form = document.getElementById('deleteVignetteForm');
            const detailsHolder = document.getElementById('deleteVignetteDetails');

            if (action && form) form.action = action;
            if (detailsHolder) detailsHolder.textContent = details || '—';
        });
    }

    /* ======================
     | OPEN CREATE MODAL ON ERRORS
     ====================== */
    @if ($errors->any())
        const addEl = document.getElementById('add_vignette');
        if (addEl) new bootstrap.Modal(addEl).show();
    @endif

});

</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ======================
     | BOOTSTRAP VALIDATION
     ====================== */
    const forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    /* ======================
     | DELETE VIGNETTE MODAL
     ====================== */
    const deleteVignetteModal = document.getElementById('delete_vignette');
    if (deleteVignetteModal) {
        deleteVignetteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;

            const action = button ? button.getAttribute('data-delete-action') : null;
            const details = button ? button.getAttribute('data-delete-details') : null;

            const form = document.getElementById('deleteVignetteForm');
            const text = document.getElementById('deleteVignetteText');

            if (action && form) form.action = action;
            if (text && details) text.innerHTML = details;
        });
    }

});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ======================
     | DELETE VIGNETTE MODAL
     ====================== */
    const deleteVignetteModal = document.getElementById('delete_vignette');
    if (deleteVignetteModal) {
        deleteVignetteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            
            // Get the delete URL from the button's data attribute
            const deleteUrl = button.getAttribute('data-delete-url');
            
            // Get the vignette details for the confirmation text
            const vignetteDate = button.getAttribute('data-vignette-date');
            const vignetteYear = button.getAttribute('data-vignette-year');
            const vignetteAmount = button.getAttribute('data-vignette-amount');
            
            // Update the form action
            const form = document.getElementById('deleteVignetteForm');
            if (form && deleteUrl) {
                form.action = deleteUrl;
            }
            
            // Update the confirmation text
            const textElement = document.getElementById('deleteVignetteText');
            if (textElement) {
                textElement.innerHTML = `Êtes-vous sûr de vouloir supprimer la vignette <strong>${vignetteYear}</strong> (${vignetteAmount}) ?`;
            }
        });
    }

});
</script>

