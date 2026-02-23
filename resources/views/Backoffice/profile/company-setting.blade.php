<?php $page = 'company-settings'; ?>
@extends('layout.mainlayout_admin')

@section('content')
    <div class="page-wrapper">
        <div class="content me-4 pb-0">

            <!-- Breadcrumb -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h2 class="mb-1">Paramètres</h2>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ url('admin/index') }}">Accueil</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Paramètres</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- /Breadcrumb -->

            @if(session('toast'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Succès!</strong> {{ session('toast')['message'] }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-xl-3">
                    <!-- inner sidebar -->
                    @include('Backoffice.profile.partials._agency_settings_sidebar', [
                        'agency' => $agency,
                        'active' => 'company',
                    ])
                    <!-- /inner sidebar -->
                </div>

                <div class="col-xl-9">
                    <div class="card">
                        <div class="card-header">
                            <h5>Paramètres Société</h5>
                        </div>

                        <form action="{{ route('backoffice.agencies.settings.update.company', $agency) }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="card-body">

                                <!-- LOGO + SIGNATURE -->
                                <div class="localization-content mb-3">
                                    <h6 class="mb-3">Identité visuelle</h6>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="fw-medium text-gray-9 mb-1">Logo</p>
                                            <div class="d-flex align-items-center flex-wrap row-gap-3 mb-3">
                                                <div class="position-relative me-3">
                                                    <div class="d-flex align-items-center justify-content-center avatar avatar-xxl flex-shrink-0 text-dark frames">
                                                        <img src="{{ $agency->getFirstMediaUrl('logo') ?: URL::asset('admin_assets/img/settings/company-logo-01.jpg') }}"
                                                            class="rounded-circle" alt="Logo" id="logoPreview" style="width: 100px; height: 100px; object-fit: cover;">
                                                    </div>
                                                    
                                                    <!-- Delete button for logo -->
                                                    <a href="javascript:void(0);" 
                                                       class="upload-img-trash btn btn-sm rounded-circle position-absolute delete-photo-btn" 
                                                       id="deleteLogoBtn"
                                                       data-type="logo"
                                                       style="top: -5px; right: -5px; display: none;">
                                                        <i class="ti ti-trash fs-12"></i>
                                                    </a>
                                                </div>

                                                <div class="profile-upload">
                                                    <div class="profile-uploader d-flex align-items-center">
                                                        <div class="drag-upload-btn btn btn-md btn-dark">
                                                            <i class="ti ti-photo-up fs-14"></i> Changer
                                                            <input type="file" name="logo" id="logoInput"
                                                                class="form-control image-sign" accept="image/*">
                                                        </div>
                                                    </div>
                                                    <div class="mt-2">
                                                        <p class="fs-14 mb-0">Taille recommandée : 500px × 500px</p>
                                                    </div>
                                                    @error('logo')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <p class="fw-medium text-gray-9 mb-1">Signature</p>
                                            <div class="d-flex align-items-center flex-wrap row-gap-3 mb-3">
                                                <div class="position-relative me-3">
                                                    <div class="d-flex align-items-center justify-content-center avatar avatar-xxl flex-shrink-0 text-dark frames">
                                                        <img src="{{ $agency->getFirstMediaUrl('signature') ?: URL::asset('admin_assets/img/settings/company-logo-01.jpg') }}"
                                                            class="rounded-circle" alt="Signature" id="signaturePreview" style="width: 100px; height: 100px; object-fit: cover;">
                                                    </div>
                                                    
                                                    <!-- Delete button for signature -->
                                                    <a href="javascript:void(0);" 
                                                       class="upload-img-trash btn btn-sm rounded-circle position-absolute delete-photo-btn" 
                                                       id="deleteSignatureBtn"
                                                       data-type="signature"
                                                       style="top: -5px; right: -5px; display: none;">
                                                        <i class="ti ti-trash fs-12"></i>
                                                    </a>
                                                </div>

                                                <div class="profile-upload">
                                                    <div class="profile-uploader d-flex align-items-center">
                                                        <div class="drag-upload-btn btn btn-md btn-dark">
                                                            <i class="ti ti-photo-up fs-14"></i> Changer
                                                            <input type="file" name="signature" id="signatureInput"
                                                                class="form-control image-sign" accept="image/*">
                                                        </div>
                                                    </div>
                                                    <div class="mt-2">
                                                        <p class="fs-14 mb-0">Signature scannée (PNG/JPG recommandé)</p>
                                                    </div>
                                                    @error('signature')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- INFOS DE BASE -->
                                <div class="localization-content mb-3">
                                    <h6 class="mb-3">Informations de base</h6>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Nom de l’agence <span class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                                    value="{{ old('name', $agency->settings['name'] ?? $agency->name ?? '') }}">
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Raison sociale</label>
                                                <input type="text" name="legal_name" class="form-control @error('legal_name') is-invalid @enderror"
                                                    value="{{ old('legal_name', $agency->settings['legal_name'] ?? '') }}">
                                                @error('legal_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Email</label>
                                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                                    value="{{ old('email', $agency->settings['email'] ?? $agency->email ?? '') }}">
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Téléphone</label>
                                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                                    value="{{ old('phone', $agency->settings['phone'] ?? $agency->phone ?? '') }}">
                                                @error('phone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Site web</label>
                                                <input type="text" name="website" class="form-control @error('website') is-invalid @enderror"
                                                    value="{{ old('website', $agency->settings['website'] ?? '') }}">
                                                @error('website')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Devise par défaut</label>
                                                <input type="text" name="default_currency" class="form-control @error('default_currency') is-invalid @enderror"
                                                    value="{{ old('default_currency', $agency->settings['default_currency'] ?? 'MAD') }}">
                                                @error('default_currency')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label">Description</label>
                                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $agency->settings['description'] ?? '') }}</textarea>
                                                @error('description')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- IDENTIFIANTS -->
                                <div class="localization-content mb-3">
                                    <h6 class="mb-3">Identifiants légaux</h6>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">TP</label>
                                                <input type="text" name="tp_number" class="form-control"
                                                    value="{{ old('tp_number', $agency->settings['tp_number'] ?? '') }}">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">RC</label>
                                                <input type="text" name="rc_number" class="form-control"
                                                    value="{{ old('rc_number', $agency->settings['rc_number'] ?? '') }}">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">IF</label>
                                                <input type="text" name="if_number" class="form-control"
                                                    value="{{ old('if_number', $agency->settings['if_number'] ?? '') }}">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">ICE</label>
                                                <input type="text" name="ice_number" class="form-control"
                                                    value="{{ old('ice_number', $agency->settings['ice_number'] ?? '') }}">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">N° TVA</label>
                                                <input type="text" name="vat_number" class="form-control"
                                                    value="{{ old('vat_number', $agency->settings['vat_number'] ?? '') }}">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Date de création</label>
                                                <input type="date" name="creation_date" class="form-control"
                                                    value="{{ old('creation_date', $agency->settings['creation_date'] ?? '') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ADRESSE -->
                                <div class="localization-content mb-3">
                                    <h6 class="mb-3">Adresse</h6>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label">Adresse</label>
                                                <input type="text" name="address" class="form-control"
                                                    value="{{ old('address', $agency->settings['address'] ?? $agency->address ?? '') }}">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Ville</label>
                                                <input type="text" name="city" class="form-control"
                                                    value="{{ old('city', $agency->settings['city'] ?? $agency->city ?? '') }}">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Pays</label>
                                                <input type="text" name="country" class="form-control"
                                                    value="{{ old('country', $agency->settings['country'] ?? $agency->country ?? '') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="card-footer">
                                <div class="d-flex align-items-center justify-content-end">
                                    <a href="{{ url('admin/index') }}" class="btn btn-light me-2">Annuler</a>
                                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

        </div>

        <div class="footer d-sm-flex align-items-center justify-content-between bg-white p-3">
            <p class="mb-0">
                <a href="javascript:void(0);">Politique de confidentialité</a>
                <a href="javascript:void(0);" class="ms-4">Conditions d’utilisation</a>
            </p>
            <p>&copy; 2025 Dreamsrent, Made with <span class="text-danger">❤</span> by <a href="javascript:void(0);"
                    class="text-secondary">Dreams</a></p>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Logo elements
        const logoInput = document.getElementById('logoInput');
        const logoPreview = document.getElementById('logoPreview');
        const deleteLogoBtn = document.getElementById('deleteLogoBtn');
        
        // Signature elements
        const signatureInput = document.getElementById('signatureInput');
        const signaturePreview = document.getElementById('signaturePreview');
        const deleteSignatureBtn = document.getElementById('deleteSignatureBtn');
        
        const defaultLogo = "{{ URL::asset('admin_assets/img/settings/company-logo-01.jpg') }}";
        const defaultSignature = "{{ URL::asset('admin_assets/img/settings/company-logo-01.jpg') }}";
        
        // Check if custom logo exists
        @if($agency->getFirstMediaUrl('logo'))
            deleteLogoBtn.style.display = 'flex';
        @endif
        
        // Check if custom signature exists
        @if($agency->getFirstMediaUrl('signature'))
            deleteSignatureBtn.style.display = 'flex';
        @endif
        
        // Logo preview on change
        if (logoInput) {
            logoInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        logoPreview.src = e.target.result;
                        deleteLogoBtn.style.display = 'flex';
                    }
                    reader.readAsDataURL(file);
                }
            });
        }
        
        // Signature preview on change
        if (signatureInput) {
            signatureInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        signaturePreview.src = e.target.result;
                        deleteSignatureBtn.style.display = 'flex';
                    }
                    reader.readAsDataURL(file);
                }
            });
        }
        
        // Delete logo with AJAX
        if (deleteLogoBtn) {
            deleteLogoBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                if (confirm('Voulez-vous vraiment supprimer le logo?')) {
                    // Show loading state
                    const originalHtml = deleteLogoBtn.innerHTML;
                    deleteLogoBtn.innerHTML = '<i class="ti ti-loader fs-12"></i>';
                    deleteLogoBtn.style.pointerEvents = 'none';
                    
                    fetch('{{ route("backoffice.agencies.settings.delete-logo", $agency) }}', {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            logoPreview.src = defaultLogo;
                            logoInput.value = '';
                            deleteLogoBtn.style.display = 'none';
                            
                            // Show success message (using toast if available)
                            if (typeof showToast === 'function') {
                                showToast('Succès', 'Logo supprimé avec succès', '#28a745');
                            } else {
                                alert('Logo supprimé avec succès');
                            }
                        } else {
                            alert(data.message || 'Erreur lors de la suppression');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Une erreur est survenue lors de la suppression');
                    })
                    .finally(() => {
                        // Restore button
                        deleteLogoBtn.innerHTML = originalHtml;
                        deleteLogoBtn.style.pointerEvents = 'auto';
                    });
                }
            });
        }
        
        // Delete signature with AJAX
        if (deleteSignatureBtn) {
            deleteSignatureBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                if (confirm('Voulez-vous vraiment supprimer la signature?')) {
                    // Show loading state
                    const originalHtml = deleteSignatureBtn.innerHTML;
                    deleteSignatureBtn.innerHTML = '<i class="ti ti-loader fs-12"></i>';
                    deleteSignatureBtn.style.pointerEvents = 'none';
                    
                    fetch('{{ route("backoffice.agencies.settings.delete-signature", $agency) }}', {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            signaturePreview.src = defaultSignature;
                            signatureInput.value = '';
                            deleteSignatureBtn.style.display = 'none';
                            
                            // Show success message
                            if (typeof showToast === 'function') {
                                showToast('Succès', 'Signature supprimée avec succès', '#28a745');
                            } else {
                                alert('Signature supprimée avec succès');
                            }
                        } else {
                            alert(data.message || 'Erreur lors de la suppression');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Une erreur est survenue lors de la suppression');
                    })
                    .finally(() => {
                        // Restore button
                        deleteSignatureBtn.innerHTML = originalHtml;
                        deleteSignatureBtn.style.pointerEvents = 'auto';
                    });
                }
            });
        }
    });

    // Optional toast function if your layout doesn't have one
    function showToast(title, message, color) {
        // You can implement a custom toast here
        // For now, we'll use alert as fallback
        alert(message);
    }
    </script>

    <style>
    .frames {
        border-radius: 50%;
        overflow: hidden;
        border: 2px solid #e0e0e0;
    }
    
    .upload-img-trash {
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: white;
        border: 1px solid #ddd;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        transition: all 0.2s;
        z-index: 10;
    }
    
    .upload-img-trash:hover {
        background: #dc3545;
        border-color: #dc3545;
        color: white !important;
    }
    
    .upload-img-trash:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .drag-upload-btn {
        position: relative;
        overflow: hidden;
        cursor: pointer;
    }
    
    .drag-upload-btn input[type="file"] {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }
    
    .avatar-xxl {
        width: 100px;
        height: 100px;
    }
    </style>
@endsection