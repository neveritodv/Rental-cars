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
     | DELETE CREDIT MODAL
     ====================== */
    const deleteModal = document.getElementById('delete_credit');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            
            const action = button?.getAttribute('data-delete-action');
            const details = button?.getAttribute('data-delete-details') || '—';
            
            const form = document.getElementById('deleteCreditForm');
            const text = document.getElementById('deleteCreditText');
            
            if (action && form) form.action = action;
            if (text && details) text.innerHTML = details;
        });
    }

    /* ======================
     | EDIT CREDIT MODAL
     ====================== */
    const editModal = document.getElementById('edit_credit');
    
    const setVal = (id, val) => {
        const el = document.getElementById(id);
        if (el) el.value = val ?? '';
    };

    if (editModal) {
        editModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const action = button?.getAttribute('data-edit-action');
            
            const data = {
                vehicle_id: button?.getAttribute('data-credit-vehicle') || '',
                creditor_name: button?.getAttribute('data-credit-creditor') || '',
                total_amount: button?.getAttribute('data-credit-total') || '',
                down_payment: button?.getAttribute('data-credit-down') || '',
                monthly_payment: button?.getAttribute('data-credit-monthly') || '',
                duration_months: button?.getAttribute('data-credit-duration') || '',
                interest_rate: button?.getAttribute('data-credit-interest') || '',
                start_date: button?.getAttribute('data-credit-start') || '',
                status: button?.getAttribute('data-credit-status') || '',
                notes: button?.getAttribute('data-credit-notes') || '',
            };

            const form = document.getElementById('editCreditForm');
            if (action && form) form.action = action;

            setVal('editCreditVehicle', data.vehicle_id);
            setVal('editCreditCreditor', data.creditor_name);
            setVal('editCreditTotal', data.total_amount);
            setVal('editCreditDown', data.down_payment);
            setVal('editCreditMonthly', data.monthly_payment);
            setVal('editCreditDuration', data.duration_months);
            setVal('editCreditInterest', data.interest_rate);
            setVal('editCreditStart', data.start_date);
            setVal('editCreditStatus', data.status);
            setVal('editCreditNotes', data.notes);

            if (form) form.classList.remove('was-validated');
        });

        editModal.addEventListener('hidden.bs.modal', function() {
            const form = document.getElementById('editCreditForm');
            if (form) form.classList.remove('was-validated');
        });
    }

    /* ======================
     | OPEN CREATE MODAL ON ERRORS
     ====================== */
    @if ($errors->any())
        const addModal = document.getElementById('add_credit');
        if (addModal) new bootstrap.Modal(addModal).show();
    @endif

});
</script>