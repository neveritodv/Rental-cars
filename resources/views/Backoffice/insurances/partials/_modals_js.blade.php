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
     | DELETE INSURANCE MODAL
     ====================== */
    const deleteModal = document.getElementById('delete_insurance');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            
            const action = button?.getAttribute('data-delete-action');
            const details = button?.getAttribute('data-insurance-details') || '—';
            
            const form = document.getElementById('deleteInsuranceForm');
            const detailsHolder = document.getElementById('deleteInsuranceDetails');
            
            if (action && form) form.action = action;
            if (detailsHolder) detailsHolder.textContent = details;
        });
    }

    /* ======================
     | EDIT INSURANCE MODAL
     ====================== */
    const editModal = document.getElementById('edit_insurance');
    
    const setVal = (id, val) => {
        const el = document.getElementById(id);
        if (el) el.value = val ?? '';
    };

    if (editModal) {
        editModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const action = button?.getAttribute('data-edit-action');
            
            const data = {
                company_name: button?.getAttribute('data-insurance-company') || '',
                policy_number: button?.getAttribute('data-insurance-policy') || '',
                date: button?.getAttribute('data-insurance-date') || '',
                amount: button?.getAttribute('data-insurance-amount') || '',
                next_date: button?.getAttribute('data-insurance-next-date') || '',
                notes: button?.getAttribute('data-insurance-notes') || '',
            };

            const form = document.getElementById('editInsuranceForm');
            if (action && form) form.action = action;

            setVal('editInsuranceCompany', data.company_name);
            setVal('editInsurancePolicy', data.policy_number);
            setVal('editInsuranceDate', data.date);
            setVal('editInsuranceAmount', data.amount);
            setVal('editInsuranceNextDate', data.next_date);
            setVal('editInsuranceNotes', data.notes);

            if (form) form.classList.remove('was-validated');
        });

        editModal.addEventListener('hidden.bs.modal', function() {
            const form = document.getElementById('editInsuranceForm');
            if (form) form.classList.remove('was-validated');
        });
    }

    /* ======================
     | OPEN CREATE MODAL ON ERRORS
     ====================== */
    @if ($errors->any())
        const addModal = document.getElementById('add_insurance');
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
     | DELETE INSURANCE MODAL
     ====================== */
    const deleteInsuranceModal = document.getElementById('delete_insurance');
    if (deleteInsuranceModal) {
        deleteInsuranceModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;

            const action = button ? button.getAttribute('data-delete-action') : null;
            const details = button ? button.getAttribute('data-delete-details') : null;

            const form = document.getElementById('deleteInsuranceForm');
            const text = document.getElementById('deleteInsuranceText');

            if (action && form) form.action = action;
            if (text && details) text.innerHTML = details;
        });
    }

});
</script>