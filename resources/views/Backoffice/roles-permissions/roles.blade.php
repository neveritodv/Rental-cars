<?php $page = 'roles-permissions'; ?>

@extends('layout.mainlayout_admin')

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-4">

            {{-- Breadcrumb --}}
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h4 class="mb-1">Rôles & Permissions</h4>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('backoffice.dashboard') }}">Accueil</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Rôles</li>
                        </ol>
                    </nav>
                </div>

                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                    <div class="mb-2 me-2">
                        <a href="javascript:void(0);" class="btn btn-primary d-flex align-items-center"
                            data-bs-toggle="modal" data-bs-target="#add_role">
                            <i class="ti ti-plus me-2"></i>Ajouter un rôle
                        </a>
                    </div>
                </div>
            </div>
            <!-- /Breadcrumb -->

            <!-- Table Header (Search) -->
            <div class="d-flex align-items-center justify-content-end flex-wrap row-gap-3 mb-3">
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                    <div class="top-search me-2">
                        <div class="top-search-group">
                            <span class="input-icon">
                                <i class="ti ti-search"></i>
                            </span>
                            <input type="text" class="form-control" id="rolesSearchInput"
                                placeholder="Rechercher des rôles...">
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Table Header -->

            <!-- Roles Table -->
            <div class="card">
                <div class="card-body">
                    <div class="custom-datatable-filter table-responsive">
                        <table class="table">
                            <thead class="thead-light">
                                <tr>
                                    <th>RÔLE</th>
                                    <th>PERMISSIONS</th>
                                    <th>DATE CRÉATION</th>
                                    <th>STATUS</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($roles as $role)
                                    @php
                                        $permCount = $role->permissions ? $role->permissions->count() : 0;
                                    @endphp
                                    <tr class="role-row" data-role-name="{{ strtolower($role->name) }}">
                                        <td>
                                            <p class="text-gray-9 mb-0"><strong>{{ $role->name }}</strong></p>
                                        </td>
                                        <td>
                                            <p class="text-gray-9 mb-0">{{ $permCount }} permission(s)</p>
                                        </td>
                                        <td>
                                            <p class="text-gray-9 mb-0">{{ optional($role->created_at)->format('d M Y') }}
                                            </p>
                                        </td>
                                        <td>
                                            <span class="badge badge-dark-transparent">
                                                <i class="ti ti-point-filled text-success me-1"></i>Active
                                            </span>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <i class="ti ti-dots-vertical"></i>
                                                </button>

                                                <ul class="dropdown-menu dropdown-menu-end p-2">
                                                    <li>
                                                        <a class="dropdown-item rounded-1"
                                                            href="{{ route('backoffice.roles-permissions.permissions', $role->id) }}">
                                                            <i class="ti ti-shield me-1"></i>Permissions
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item rounded-1" href="javascript:void(0);"
                                                            data-bs-toggle="modal" data-bs-target="#edit_role"
                                                            data-edit-action="{{ route('backoffice.roles.update', $role->id) }}"
                                                            data-role-name="{{ $role->name }}"
                                                            data-role-permissions='@json($role->permissions->pluck('id')->values())'>
                                                            <i class="ti ti-edit me-1"></i>Modifier
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <a class="dropdown-item rounded-1" href="javascript:void(0);"
                                                            data-bs-toggle="modal" data-bs-target="#delete_role"
                                                            data-delete-action="{{ route('backoffice.roles.destroy', $role->id) }}"
                                                            data-delete-name="{{ $role->name }}">
                                                            <i class="ti ti-trash me-1"></i>Supprimer
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">
                                            <p class="text-gray-5 mb-0 text-center">Aucun rôle trouvé.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if ($roles->hasPages())
                        <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mt-4">
                            <div>
                                <p class="text-gray-9 mb-0">
                                    Affichage {{ $roles->firstItem() }} à {{ $roles->lastItem() }} sur
                                    {{ $roles->total() }} rôle(s)
                                </p>
                            </div>
                            <nav aria-label="Page navigation">
                                {{ $roles->links() }}
                            </nav>
                        </div>
                    @endif
                </div>
            </div>
            <!-- /Roles Table -->

        </div>

        <!-- Footer -->
        <div class="footer d-sm-flex align-items-center justify-content-between bg-white p-3">
            <p class="mb-0">
                <a href="javascript:void(0);">Privacy Policy</a>
                <a href="javascript:void(0);" class="ms-4">Terms of Use</a>
            </p>
            <p>&copy; 2025 Dreamsrent, Made with <span class="text-danger">❤</span> by
                <a href="javascript:void(0);" class="text-secondary">Dreams</a>
            </p>
        </div>
        <!-- /Footer -->
    </div>
    <!-- /Page Wrapper -->

    {{-- Modals --}}
    @include('backoffice.roles-permissions.partials._modal_create')
    @include('backoffice.roles-permissions.partials._modal_edit')
    @include('backoffice.roles-permissions.partials._modal_delete')

    {{-- JS --}}
    @include('backoffice.roles-permissions.partials._modals_js')

    {{-- Search filter --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('rolesSearchInput');
            if (!input) return;

            input.addEventListener('input', function() {
                const q = (this.value || '').toLowerCase().trim();

                const rows = document.querySelectorAll('table tbody .role-row');
                rows.forEach(row => {
                    const roleName = (row.getAttribute('data-role-name') || '').toLowerCase();
                    row.style.display = roleName.includes(q) ? '' : 'none';
                });
            });
        });
    </script>
@endsection
