<?php $page = 'invoice-template'; ?>
@extends('layout.mainlayout_admin')
@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-0 pb-0 me-lg-4">

            <!-- Breadcrumb -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h2 class="mb-1">Agency Settings</h2>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('backoffice.dashboard') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('backoffice.agencies.index') }}">Agencies</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Invoice Template</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- /Breadcrumb -->

            <!-- Settings Prefix -->
            <div class="row">
                <div class="col-lg-3">
                    <!-- inner sidebar -->
                    @include('Backoffice.profile.partials._agency_settings_sidebar', [
                        'agency' => $agency,
                        'active' => 'invoice-template',
                    ])
                    <!-- /inner sidebar -->
                </div>
                <div class="col-lg-9">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="fw-bold">Invoice Template Selection</h5>
                        </div>
                        <form action="{{ route('backoffice.agencies.settings.update', $agency) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="card-body pb-3">
                                <div class="mb-3">
                                    <label class="form-label">Select Invoice Template <span
                                            class="text-danger">*</span></label>
                                    <select name="app[invoice_template]" class="form-control">
                                        <option value="">Choose Template</option>
                                        <option value="modern"
                                            {{ ($agency->settings['invoice_template'] ?? '') === 'modern' ? 'selected' : '' }}>
                                            Modern</option>
                                        <option value="classic"
                                            {{ ($agency->settings['invoice_template'] ?? '') === 'classic' ? 'selected' : '' }}>
                                            Classic</option>
                                        <option value="minimal"
                                            {{ ($agency->settings['invoice_template'] ?? '') === 'minimal' ? 'selected' : '' }}>
                                            Minimal</option>
                                    </select>
                                    @error('app.invoice_template')
                                        <small class="text-danger d-block mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="d-flex align-items-center justify-content-end">
                                    <a href="{{ route('backoffice.agencies.index') }}" class="btn btn-light me-2">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- /Settings Prefix -->
        </div>
        <!-- Footer-->
        <div class="footer d-sm-flex align-items-center justify-content-between bg-white p-3">
            <p class="mb-0">
                <a href="javascript:void(0);">Privacy Policy</a>
                <a href="javascript:void(0);" class="ms-4">Terms of Use</a>
            </p>
            <p>&copy; 2025 Dreamsrent, Made with <span class="text-danger">❤</span> by <a href="javascript:void(0);"
                    class="text-secondary">Dreams</a></p>
        </div>
        <!-- /Footer-->
    </div>
    <!-- /Page Wrapper -->
@endsection
