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
     | EDIT CLIENT MODAL
     ====================== */
    const editModal = document.getElementById('edit_client');

    const setVal = (id, val) => {
        const el = document.getElementById(id);
        if (el !== null) el.value = val ?? '';
    };

    if (editModal) {
        editModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;

            const action = button ? button.getAttribute('data-edit-action') : null;

            const data = {
                first_name: button?.getAttribute('data-client-first-name') || '',
                last_name: button?.getAttribute('data-client-last-name') || '',
                email: button?.getAttribute('data-client-email') || '',
                phone: button?.getAttribute('data-client-phone') || '',
                birth_date: button?.getAttribute('data-client-birth-date') || '',
                nationality: button?.getAttribute('data-client-nationality') || '',
                address: button?.getAttribute('data-client-address') || '',
                city: button?.getAttribute('data-client-city') || '',
                country: button?.getAttribute('data-client-country') || '',
                status: button?.getAttribute('data-client-status') || 'active',
                cin_number: button?.getAttribute('data-client-cin-number') || '',
                cin_valid_until: button?.getAttribute('data-client-cin-valid-until') || '',
                passport_number: button?.getAttribute('data-client-passport-number') || '',
                passport_issue_date: button?.getAttribute('data-client-passport-issue-date') || '',
                driving_license_number: button?.getAttribute('data-client-driving-license-number') || '',
                driving_license_issue_date: button?.getAttribute('data-client-driving-license-issue-date') || '',
                notes: button?.getAttribute('data-client-notes') || '',
                agency_id: button?.getAttribute('data-client-agency') || '',
                avatar: button?.getAttribute('data-client-avatar') || '',
            };

            const form = document.getElementById('editClientForm');
            if (action && form) form.action = action;

            setVal('editClientFirstName', data.first_name);
            setVal('editClientLastName', data.last_name);
            setVal('editClientEmail', data.email);
            setVal('editClientPhone', data.phone);
            setVal('editClientBirthDate', data.birth_date);
            setVal('editClientNationality', data.nationality);
            setVal('editClientAddress', data.address);
            setVal('editClientCity', data.city);
            setVal('editClientCountry', data.country);
            setVal('editClientStatus', data.status);
            setVal('editClientCinNumber', data.cin_number);
            setVal('editClientCinValidUntil', data.cin_valid_until);
            setVal('editClientPassportNumber', data.passport_number);
            setVal('editClientPassportIssueDate', data.passport_issue_date);
            setVal('editClientDrivingLicenseNumber', data.driving_license_number);
            setVal('editClientDrivingLicenseIssueDate', data.driving_license_issue_date);
            setVal('editClientNotes', data.notes);
            setVal('editClientAgency', data.agency_id);

            // Handle avatar
            const avatarImg = document.getElementById('editClientAvatarImg');
            const avatarIcon = document.getElementById('editClientAvatarIcon');
            const avatarTrash = document.getElementById('editClientAvatarTrash');
            const removeInput = document.getElementById('editClientRemoveAvatar');
            
            if (removeInput) removeInput.value = '0';
            
            if (data.avatar && data.avatar !== '' && data.avatar !== 'null') {
                avatarImg.src = data.avatar;
                avatarImg.style.display = '';
                avatarIcon.style.display = 'none';
                avatarTrash.style.display = '';
            } else {
                avatarImg.src = '';
                avatarImg.style.display = 'none';
                avatarIcon.style.display = '';
                avatarTrash.style.display = 'none';
            }

            if (form) form.classList.remove('was-validated');
        });

        editModal.addEventListener('hidden.bs.modal', function() {
            const form = document.getElementById('editClientForm');
            if (form) form.classList.remove('was-validated');
        });
    }

    /* ======================
     | DELETE CLIENT MODAL
     ====================== */
    const deleteModal = document.getElementById('delete_client');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;

            const action = button ? button.getAttribute('data-delete-action') : null;
            const name = button ? button.getAttribute('data-client-name') : '—';

            const form = document.getElementById('deleteClientForm');
            const nameHolder = document.getElementById('deleteClientName');

            if (action && form) form.action = action;
            if (nameHolder) nameHolder.textContent = name || '—';
        });
    }

    /* ======================
     | OPEN CREATE MODAL ON ERRORS
     ====================== */
    @if ($errors->any())
        const addEl = document.getElementById('add_client');
        if (addEl) new bootstrap.Modal(addEl).show();
    @endif

});

/* ======================
 | ADD CLIENT AVATAR UPLOAD
 ====================== */
document.addEventListener('DOMContentLoaded', function () {
    const addAvatarInput = document.getElementById('addClientAvatarInput');
    const addAvatarImg = document.getElementById('addClientAvatarImg');
    const addAvatarIcon = document.getElementById('addClientAvatarIcon');
    const addAvatarClear = document.getElementById('addClientAvatarClearBtn');
    const addAvatarFrame = document.getElementById('addClientAvatarFrame');

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

    if (addAvatarInput) {
        addAvatarInput.addEventListener('change', function () {
            const file = this.files && this.files[0] ? this.files[0] : null;
            
            if (!file) return resetAvatar();

            if (file.size > 2 * 1024 * 1024) {
                alert('L\'image ne doit pas dépasser 2MB');
                resetAvatar();
                return;
            }

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

    if (addAvatarClear) {
        addAvatarClear.addEventListener('click', resetAvatar);
    }

    const addClientModal = document.getElementById('add_client');
    if (addClientModal) {
        addClientModal.addEventListener('hidden.bs.modal', function () {
            resetAvatar();
            
            const form = document.getElementById('addClientForm');
            if (form) {
                form.reset();
                form.classList.remove('was-validated');
            }
        });
    }
});

/* ======================
 | EDIT CLIENT AVATAR UPLOAD
 ====================== */
document.addEventListener('DOMContentLoaded', function () {
    const editAvatarInput = document.getElementById('editClientAvatarInput');
    const editAvatarImg = document.getElementById('editClientAvatarImg');
    const editAvatarIcon = document.getElementById('editClientAvatarIcon');
    const editAvatarTrash = document.getElementById('editClientAvatarTrash');
    const editRemoveInput = document.getElementById('editClientRemoveAvatar');
    const editAvatarFrame = document.getElementById('editClientAvatarFrame');

    if (editAvatarInput) {
        editAvatarInput.addEventListener('change', function() {
            const file = this.files && this.files[0] ? this.files[0] : null;

            if (editRemoveInput) editRemoveInput.value = '0';
            if (!file) return;

            if (file.size > 2 * 1024 * 1024) {
                alert('L\'image ne doit pas dépasser 2MB');
                this.value = '';
                return;
            }

            if (!file.type.match('image.*')) {
                alert('Veuillez sélectionner une image valide');
                this.value = '';
                return;
            }

            const url = URL.createObjectURL(file);
            editAvatarImg.src = url;
            editAvatarImg.style.display = '';
            editAvatarIcon.style.display = 'none';
            editAvatarTrash.style.display = '';
            if (editAvatarFrame) editAvatarFrame.classList.add('border-primary');
        });
    }

    if (editAvatarTrash) {
        editAvatarTrash.addEventListener('click', function() {
            if (editRemoveInput) editRemoveInput.value = '1';
            if (editAvatarInput) editAvatarInput.value = '';
            editAvatarImg.src = '';
            editAvatarImg.style.display = 'none';
            editAvatarIcon.style.display = '';
            editAvatarTrash.style.display = 'none';
            if (editAvatarFrame) editAvatarFrame.classList.remove('border-primary');
        });
    }
});

/* ======================
 | ADD CLIENT AVATAR PREVIEW
 ====================== */
const addLogoInput = document.getElementById('addClientLogoInput');
const addPreview = document.getElementById('addClientPreview');
const addIcon = document.getElementById('addClientIcon');
const addModalEl = document.getElementById('add_client');

function resetAddPreview() {
    if (addPreview) {
        addPreview.src = '';
        addPreview.style.display = 'none';
    }
    if (addIcon) addIcon.style.display = '';
    if (addLogoInput) addLogoInput.value = '';

    const clientErr = document.getElementById('addClientLogoClientError');
    if (clientErr) clientErr.style.display = 'none';
}

if (addLogoInput && addPreview) {
    addLogoInput.addEventListener('change', function () {
        const file = this.files && this.files[0] ? this.files[0] : null;

        if (!file || !file.type || !file.type.startsWith('image/')) {
            resetAddPreview();
            return;
        }

        // Validate file size (2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('L\'image ne doit pas dépasser 2MB');
            resetAddPreview();
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            addPreview.src = e.target.result;
            addPreview.style.display = 'block';
            if (addIcon) addIcon.style.display = 'none';
        };
        reader.readAsDataURL(file);
    });
}

if (addModalEl) {
    addModalEl.addEventListener('hidden.bs.modal', function () {
        resetAddPreview();
        const form = addModalEl.querySelector('form.needs-validation');
        if (form) form.classList.remove('was-validated');
    });
}

/* ======================
 | SELECT ALL CHECKBOX
 ====================== */
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('select-all');
    if (selectAll) {
        selectAll.addEventListener('change', function () {
            let checkboxes = document.querySelectorAll('.client-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    }
});
</script>