<?php $page = 'change-password'; ?>
@extends('layout.mainlayout_admin')

@section('content')
    <div class="page-wrapper">

        <div class="content me-4 pb-0">

            <!-- Breadcrumb -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h2 class="mb-1">Change Password</h2>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('backoffice.dashboard') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item active">Change Password</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- /Breadcrumb -->

            <div class="row">
                <!-- Sidebar -->
                <div class="col-lg-3 mb-3 mb-lg-0">
                    @include('Backoffice.profile.partials._agency_settings_sidebar', [
                        'active' => 'change-password',
                        'agency' => $agency,
                    ])
                </div>

                <!-- Main Content -->
                <div class="col-lg-9">

                    <div class="card">
                        <div class="card-header">
                            <h5>Security Settings</h5>
                        </div>

                        <div class="card-body">

                            <!-- PASSWORD SECTION -->
                            <div class="card mb-0">
                                <div class="card-body">
                                    <div class="row gy-3 align-items-center">
                                        <div class="col-lg-9">
                                            <div class="row gy-3 align-items-center">
                                                <div class="col-md-6">
                                                    <div>
                                                        <h6 class="fs-14 fw-medium">Password</h6>
                                                        <p class="fs-13">Set a unique password to secure the account</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div>
                                                        @if (auth('backoffice')->user()->password_changed_at)
                                                            <p class="mb-0">
                                                                <i class="ti ti-circle-check-filled text-success me-1"></i>
                                                                Last Changed
                                                                {{ auth('backoffice')->user()->password_changed_at->format('d M Y, g:i A') }}
                                                            </p>
                                                        @else
                                                            <p class="mb-0">
                                                                <i class="ti ti-alert-circle text-warning me-1"></i>
                                                                Password never changed
                                                            </p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-3">
                                            <div class="d-flex justify-content-lg-end">
                                                <a href="javascript:void(0);" class="btn btn-dark" data-bs-toggle="modal"
                                                    data-bs-target="#change_password">
                                                    Change
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /PASSWORD SECTION -->

                            <!-- CHANGE PASSWORD MODAL -->
                            <div class="modal fade" id="change_password" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Change Password</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>

                                        <form method="POST" action="{{ route('backoffice.profile.update-password') }}">
                                            @csrf
                                            @method('PUT')

                                            <div class="modal-body">

                                                <div class="mb-3">
                                                    <label class="form-label">Current Password</label>
                                                    <input type="password" name="current_password"
                                                        class="form-control @error('current_password') is-invalid @enderror"
                                                        autocomplete="current-password">
                                                    @error('current_password')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">New Password</label>
                                                    <input type="password" name="password"
                                                        class="form-control @error('password') is-invalid @enderror"
                                                        autocomplete="new-password">
                                                    @error('password')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-0">
                                                    <label class="form-label">Confirm New Password</label>
                                                    <input type="password" name="password_confirmation" class="form-control"
                                                        autocomplete="new-password">
                                                </div>

                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                                    Cancel
                                                </button>
                                                <button type="submit" class="btn btn-dark">
                                                    Update Password
                                                </button>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>
                            <!-- /CHANGE PASSWORD MODAL -->

                        </div>
                    </div>

                </div>
            </div>

        </div>

        <!-- Footer -->
        <div class="footer d-sm-flex align-items-center justify-content-between bg-white p-3">
            <p class="mb-0">
                <a href="javascript:void(0);">Privacy Policy</a>
                <a href="javascript:void(0);" class="ms-4">Terms of Use</a>
            </p>
            <p>&copy; 2025 Dreamsrent, Made with
                <span class="text-danger">❤</span>
                by <a href="javascript:void(0);" class="text-secondary">Dreams</a>
            </p>
        </div>

    </div>

    {{-- Success Toast --}}
    @if (session('success'))
        <div class="toast-container position-fixed top-0 end-0 p-3">
            <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header bg-success text-white">
                    <i class="ti ti-check-circle me-2"></i>
                    <strong class="me-auto">Success</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    {{ session('success') }}
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const toastElements = document.querySelectorAll('.toast');
                toastElements.forEach(function(toastEl) {
                    const toast = new bootstrap.Toast(toastEl);
                    toast.show();
                    // Auto hide after 5 seconds
                    setTimeout(() => toast.hide(), 5000);
                });
            });
        </script>
    @endif

    {{-- Auto-open modal if validation failed --}}
    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modalEl = document.getElementById('change_password');
                if (modalEl && window.bootstrap) {
                    new bootstrap.Modal(modalEl).show();
                }
            });
        </script>
    @endif
@endsection
