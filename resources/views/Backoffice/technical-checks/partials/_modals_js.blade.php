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
     | DELETE TECHNICAL CHECK MODAL
     ====================== */
    const deleteModal = document.getElementById('delete_technical_check');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            
            const action = button?.getAttribute('data-delete-action');
            const details = button?.getAttribute('data-technical-check-details') || '—';
            
            const form = document.getElementById('deleteTechnicalCheckForm');
            const detailsHolder = document.getElementById('deleteTechnicalCheckDetails');
            
            if (action && form) form.action = action;
            if (detailsHolder) detailsHolder.textContent = details;
        });
    }

    /* ======================
     | EDIT TECHNICAL CHECK MODAL
     ====================== */
    const editModal = document.getElementById('edit_technical_check');
    
    const setVal = (id, val) => {
        const el = document.getElementById(id);
        if (el) el.value = val ?? '';
    };

    if (editModal) {
        editModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const action = button?.getAttribute('data-edit-action');
            
            const data = {
                date: button?.getAttribute('data-technical-check-date') || '',
                amount: button?.getAttribute('data-technical-check-amount') || '',
                next_date: button?.getAttribute('data-technical-check-next-date') || '',
                notes: button?.getAttribute('data-technical-check-notes') || '',
            };

            const form = document.getElementById('editTechnicalCheckForm');
            if (action && form) form.action = action;

            setVal('editTechnicalCheckDate', data.date);
            setVal('editTechnicalCheckAmount', data.amount);
            setVal('editTechnicalCheckNextDate', data.next_date);
            setVal('editTechnicalCheckNotes', data.notes);

            if (form) form.classList.remove('was-validated');
        });

        editModal.addEventListener('hidden.bs.modal', function() {
            const form = document.getElementById('editTechnicalCheckForm');
            if (form) form.classList.remove('was-validated');
        });
    }

    /* ======================
     | OPEN CREATE MODAL ON ERRORS
     ====================== */
    @if ($errors->any())
        const addModal = document.getElementById('add_technical_check');
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
     | DELETE TECHNICAL CHECK MODAL
     ====================== */
    const deleteTechnicalCheckModal = document.getElementById('delete_technical_check');
    if (deleteTechnicalCheckModal) {
        deleteTechnicalCheckModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;

            const action = button ? button.getAttribute('data-delete-action') : null;
            const details = button ? button.getAttribute('data-delete-details') : null;

            const form = document.getElementById('deleteTechnicalCheckForm');
            const text = document.getElementById('deleteTechnicalCheckText');

            if (action && form) form.action = action;
            if (text && details) text.innerHTML = details;
        });
    }

});
</script>