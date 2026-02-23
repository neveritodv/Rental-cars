<script>
document.addEventListener('DOMContentLoaded', function() {
    // Bootstrap validation for any forms
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
    
    // Validate end mileage >= start mileage in create form
    const createForm = document.querySelector('form[action*="controls.store"]');
    if (createForm) {
        const startMileage = createForm.querySelector('input[name="start_mileage"]');
        const endMileage = createForm.querySelector('input[name="end_mileage"]');
        
        if (startMileage && endMileage) {
            endMileage.addEventListener('input', function() {
                if (this.value && startMileage.value && parseInt(this.value) < parseInt(startMileage.value)) {
                    this.setCustomValidity('Le kilométrage d\'arrivée doit être supérieur ou égal au kilométrage de départ');
                } else {
                    this.setCustomValidity('');
                }
            });
        }
    }
    
    // Validate end mileage >= start mileage in edit form
    const editForm = document.querySelector('form[action*="controls.update"]');
    if (editForm) {
        const startMileage = editForm.querySelector('input[name="start_mileage"]');
        const endMileage = editForm.querySelector('input[name="end_mileage"]');
        
        if (startMileage && endMileage) {
            endMileage.addEventListener('input', function() {
                if (this.value && startMileage.value && parseInt(this.value) < parseInt(startMileage.value)) {
                    this.setCustomValidity('Le kilométrage d\'arrivée doit être supérieur ou égal au kilométrage de départ');
                } else {
                    this.setCustomValidity('');
                }
            });
        }
    }
    
    console.log('Vehicle Controls Modals JS loaded');
});
</script>