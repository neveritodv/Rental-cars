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
     | DELETE OIL CHANGE MODAL
     ====================== */
    const deleteModal = document.getElementById('delete_oil_change');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            
            const action = button?.getAttribute('data-delete-action');
            const details = button?.getAttribute('data-oil-change-details') || '—';
            
            const form = document.getElementById('deleteOilChangeForm');
            const detailsHolder = document.getElementById('deleteOilChangeDetails');
            
            if (action && form) form.action = action;
            if (detailsHolder) detailsHolder.textContent = details;
        });
    }

    /* ======================
     | EDIT OIL CHANGE MODAL
     ====================== */
    const editModal = document.getElementById('edit_oil_change');
    
    const setVal = (id, val) => {
        const el = document.getElementById(id);
        if (el) el.value = val ?? '';
    };

    if (editModal) {
        editModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const action = button?.getAttribute('data-edit-action');
            
            const data = {
                date: button?.getAttribute('data-oil-change-date') || '',
                mechanic: button?.getAttribute('data-oil-change-mechanic') || '',
                mileage: button?.getAttribute('data-oil-change-mileage') || '',
                next_mileage: button?.getAttribute('data-oil-change-next-mileage') || '',
                amount: button?.getAttribute('data-oil-change-amount') || '',
                observations: button?.getAttribute('data-oil-change-observations') || '',
            };

            const form = document.getElementById('editOilChangeForm');
            if (action && form) form.action = action;

            setVal('editOilChangeDate', data.date);
            setVal('editOilChangeMechanic', data.mechanic);
            setVal('editOilChangeMileage', data.mileage);
            setVal('editOilChangeNextMileage', data.next_mileage);
            setVal('editOilChangeAmount', data.amount);
            setVal('editOilChangeObservations', data.observations);

            if (form) form.classList.remove('was-validated');
        });

        editModal.addEventListener('hidden.bs.modal', function() {
            const form = document.getElementById('editOilChangeForm');
            if (form) form.classList.remove('was-validated');
        });
    }

    /* ======================
     | OPEN CREATE MODAL ON ERRORS
     ====================== */
    @if ($errors->any())
        const addModal = document.getElementById('add_oil_change');
        if (addModal) new bootstrap.Modal(addModal).show();
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
     | DELETE OIL CHANGE MODAL
     ====================== */
    const deleteOilChangeModal = document.getElementById('delete_oil_change');
    if (deleteOilChangeModal) {
        deleteOilChangeModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;

            const action = button ? button.getAttribute('data-delete-action') : null;
            const details = button ? button.getAttribute('data-delete-details') : null;

            const form = document.getElementById('deleteOilChangeForm');
            const text = document.getElementById('deleteOilChangeText');

            if (action && form) form.action = action;
            if (text && details) text.innerHTML = details;
        });
    }

});
</script>