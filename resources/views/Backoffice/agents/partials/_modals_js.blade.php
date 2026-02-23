
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
     | EDIT AGENT MODAL
     ====================== */
    const editModal = document.getElementById('edit_agent');

    const setVal = (id, val) => {
        const el = document.getElementById(id);
        if (el !== null) el.value = val ?? '';
    };

    if (editModal) {
        editModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;

            const action = button ? button.getAttribute('data-edit-action') : null;

            const data = {
                full_name: button?.getAttribute('data-agent-full-name') || '',
                email: button?.getAttribute('data-agent-email') || '',
                phone: button?.getAttribute('data-agent-phone') || '',
                notes: button?.getAttribute('data-agent-notes') || '',
                agency_id: button?.getAttribute('data-agent-agency') || '',
                user_id: button?.getAttribute('data-agent-user') || '',
            };

            const form = document.getElementById('editAgentForm');
            if (action && form) form.action = action;

            setVal('editAgentFullName', data.full_name);
            setVal('editAgentEmail', data.email);
            setVal('editAgentPhone', data.phone);
            setVal('editAgentNotes', data.notes);
            setVal('editAgentAgency', data.agency_id);
            setVal('editAgentUser', data.user_id);

            // reset validation
            if (form) form.classList.remove('was-validated');
        });

        editModal.addEventListener('hidden.bs.modal', function() {
            const form = document.getElementById('editAgentForm');
            if (form) form.classList.remove('was-validated');
        });
    }

    /* ======================
     | DELETE AGENT MODAL
     ====================== */
    const deleteModal = document.getElementById('delete_agent');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;

            const action = button ? button.getAttribute('data-delete-action') : null;
            const name = button ? button.getAttribute('data-agent-name') : '—';

            const form = document.getElementById('deleteAgentForm');
            const nameHolder = document.getElementById('deleteAgentName');

            if (action && form) form.action = action;
            if (nameHolder) nameHolder.textContent = name || '—';
        });
    }

    /* ======================
     | OPEN CREATE MODAL ON ERRORS
     ====================== */
    @if ($errors->any())
        const addEl = document.getElementById('add_agent');
        if (addEl) new bootstrap.Modal(addEl).show();
    @endif

});

/* ======================
 | ADD AGENT AVATAR UPLOAD
 ====================== */
document.addEventListener('DOMContentLoaded', function () {
    const addAvatarInput = document.getElementById('addAgentAvatarInput');
    const addAvatarImg = document.getElementById('addAgentAvatarImg');
    const addAvatarIcon = document.getElementById('addAgentAvatarIcon');
    const addAvatarClear = document.getElementById('addAgentAvatarClearBtn');
    const addAvatarFrame = document.getElementById('addAgentAvatarFrame');

    // Reset function
    const resetAvatar = () => {
        if (addAvatarInput) addAvatarInput.value = '';
        if (addAvatarImg) {
            addAvatarImg.src = '';
            addAvatarImg.style.display = 'none';
        }
        if (addAvatarIcon) addAvatarIcon.style.display = '';
        if (addAvatarFrame) {
            addAvatarFrame.classList.remove('border-primary');
        }
    };

    // Preview image on file select
    if (addAvatarInput) {
        addAvatarInput.addEventListener('change', function () {
            const file = this.files && this.files[0] ? this.files[0] : null;
            
            if (!file) return resetAvatar();

            // Validate file size (2MB limit)
            if (file.size > 2 * 1024 * 1024) {
                alert('L\'image ne doit pas dépasser 2MB');
                resetAvatar();
                return;
            }

            // Validate file type
            if (!file.type.match('image.*')) {
                alert('Veuillez sélectionner une image valide');
                resetAvatar();
                return;
            }

            const url = URL.createObjectURL(file);
            
            if (addAvatarImg) {
                addAvatarImg.src = url;
                addAvatarImg.style.display = '';
                addAvatarImg.onload = () => URL.revokeObjectURL(url);
            }
            if (addAvatarIcon) addAvatarIcon.style.display = 'none';
            if (addAvatarFrame) addAvatarFrame.classList.add('border-primary');
        });
    }

    // Clear button
    if (addAvatarClear) {
        addAvatarClear.addEventListener('click', resetAvatar);
    }

    // Reset modal when closed
    const addAgentModal = document.getElementById('add_agent');
    if (addAgentModal) {
        addAgentModal.addEventListener('hidden.bs.modal', function () {
            resetAvatar();
            
            // Reset form
            const form = document.getElementById('addAgentForm');
            if (form) {
                form.reset();
                form.classList.remove('was-validated');
            }
        });
    }
});
</script>

<script>
    document.getElementById('select-all').addEventListener('change', function () {
        let checkboxes = document.querySelectorAll('.agent-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });
</script>


