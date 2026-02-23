<?php $page = 'agents'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<div class="page-wrapper">
    <div class="content me-0">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="mb-3">
                    <a href="{{ route('backoffice.agents.index') }}" class="d-inline-flex align-items-center fw-medium">
                        <i class="ti ti-arrow-left me-1"></i>
                        Retour à la liste
                    </a>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="ti ti-user-plus me-2"></i>
                            Ajouter un agent
                        </h4>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('backoffice.agents.store') }}" 
                              method="POST" 
                              enctype="multipart/form-data"
                              class="needs-validation" 
                              novalidate>
                            @csrf

                            <!-- Photo Upload -->
                            <div class="mb-4">
                                <label class="form-label fw-medium">Photo de l'agent</label>
                                <div class="d-flex align-items-center flex-wrap row-gap-3">
                                    <div id="avatarFrame"
                                         class="d-flex align-items-center justify-content-center avatar avatar-xxl border me-3 flex-shrink-0 text-dark frames"
                                         style="overflow:hidden;border-radius:16px; background: #f8f9fa; border: 2px dashed #dee2e6; width: 120px; height: 120px;">
                                        <i id="avatarIcon" class="ti ti-photo-up text-gray-4 fs-32"></i>
                                        <img id="avatarImg"
                                             src=""
                                             alt="Preview"
                                             style="display:none;width:100%;height:100%;object-fit:cover;">
                                    </div>

                                    <div class="profile-upload flex-grow-1">
                                        <div class="profile-uploader d-flex align-items-center flex-wrap gap-2">
                                            <div class="drag-upload-btn btn btn-md btn-dark position-relative">
                                                <i class="ti ti-upload me-1"></i>
                                                Choisir une photo
                                                <input type="file"
                                                       name="avatar"
                                                       id="avatarInput"
                                                       class="position-absolute top-0 start-0 w-100 h-100 opacity-0"
                                                       accept="image/*"
                                                       style="cursor: pointer;">
                                            </div>
                                            <button type="button" 
                                                    class="btn btn-md btn-light" 
                                                    id="avatarClearBtn">
                                                <i class="ti ti-trash me-1"></i>
                                                Effacer
                                            </button>
                                        </div>
                                        <div class="mt-2">
                                            <p class="fs-12 text-muted mb-0">
                                                <i class="ti ti-info-circle me-1"></i>
                                                Formats: JPG, PNG, GIF • Taille max: 2MB
                                            </p>
                                        </div>
                                        @error('avatar')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Agence -->
                            <div class="mb-3">
                                <label class="form-label">
                                    Agence <span class="text-danger">*</span>
                                </label>
                                <select name="agency_id"
                                        class="form-select @error('agency_id') is-invalid @enderror"
                                        required>
                                    <option value="">Sélectionner une agence</option>
                                    @forelse($agencies as $agency)
                                        <option value="{{ $agency->id }}" {{ old('agency_id') == $agency->id ? 'selected' : '' }}>
                                            {{ $agency->name }}
                                        </option>
                                    @empty
                                        <option value="" disabled>Aucune agence disponible</option>
                                    @endforelse
                                </select>
                                @error('agency_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <!-- Nom complet -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Nom complet <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                               name="full_name"
                                               value="{{ old('full_name') }}"
                                               class="form-control @error('full_name') is-invalid @enderror"
                                               required
                                               maxlength="150"
                                               placeholder="Jean Dupont">
                                        @error('full_name')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email"
                                               name="email"
                                               value="{{ old('email') }}"
                                               class="form-control @error('email') is-invalid @enderror"
                                               maxlength="150"
                                               placeholder="agent@example.com">
                                        @error('email')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Téléphone -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Téléphone</label>
                                        <input type="tel"
                                               name="phone"
                                               value="{{ old('phone') }}"
                                               class="form-control @error('phone') is-invalid @enderror"
                                               maxlength="50"
                                               placeholder="+33 1 23 45 67 89">
                                        @error('phone')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Utilisateur lié -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Utilisateur lié</label>
                                        <select name="user_id"
                                                class="form-select @error('user_id') is-invalid @enderror">
                                            <option value="">Aucun utilisateur lié</option>
                                            @forelse($users as $user)
                                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }} ({{ $user->email }})
                                                </option>
                                            @empty
                                            @endforelse
                                        </select>
                                        @error('user_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Notes -->
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Notes</label>
                                        <textarea name="notes"
                                                  class="form-control @error('notes') is-invalid @enderror"
                                                  rows="4"
                                                  placeholder="Informations complémentaires...">{{ old('notes') }}</textarea>
                                        @error('notes')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-3 mt-4">
                                <a href="{{ route('backoffice.agents.index') }}" class="btn btn-light px-4">
                                    <i class="ti ti-x me-1"></i>
                                    Annuler
                                </a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="ti ti-device-floppy me-1"></i>
                                    Créer l'agent
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const avatarInput = document.getElementById('avatarInput');
    const avatarImg = document.getElementById('avatarImg');
    const avatarIcon = document.getElementById('avatarIcon');
    const avatarClear = document.getElementById('avatarClearBtn');
    const avatarFrame = document.getElementById('avatarFrame');

    function resetAvatar() {
        if (avatarInput) avatarInput.value = '';
        if (avatarImg) {
            avatarImg.src = '';
            avatarImg.style.display = 'none';
        }
        if (avatarIcon) avatarIcon.style.display = 'flex';
        if (avatarFrame) avatarFrame.style.border = '2px dashed #dee2e6';
    }

    if (avatarInput) {
        avatarInput.addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return resetAvatar();

            if (file.size > 2 * 1024 * 1024) {
                alert('La photo ne doit pas dépasser 2MB');
                resetAvatar();
                return;
            }

            if (!file.type.match('image.*')) {
                alert('Veuillez sélectionner une image valide');
                resetAvatar();
                return;
            }

            const url = URL.createObjectURL(file);
            avatarImg.src = url;
            avatarImg.style.display = '';
            avatarIcon.style.display = 'none';
            avatarFrame.style.border = '2px solid #0d6efd';
            
            avatarImg.onload = () => URL.revokeObjectURL(url);
        });
    }

    if (avatarClear) {
        avatarClear.addEventListener('click', resetAvatar);
    }

    // Bootstrap validation
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
});
</script>
@endpush
@endsection