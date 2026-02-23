<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesPermissionsController extends Controller
{
    /**
     * Show roles index page
     */
    public function indexRoles(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $rolesQuery = Role::query()
            ->where('guard_name', 'backoffice')
            ->with(['permissions:id,name,guard_name'])
            ->orderBy('name');

        if ($q !== '') {
            $rolesQuery->where('name', 'like', "%{$q}%");
        }

        $roles = $rolesQuery->paginate(15)->withQueryString();

        // Get all permissions for the modal
        $allPermissions = Permission::query()
            ->where('guard_name', 'backoffice')
            ->orderBy('name')
            ->get();

        return view('Backoffice.roles-permissions.roles', compact('roles', 'allPermissions'));
    }

    /**
     * Show permissions management page for a specific role
     * Builds matrix with CRUD strategy: module.resource.action
     */
    public function showPermissions(Role $role, Request $request)
    {
        // Ensure the role is from backoffice guard
        if ($role->guard_name !== 'backoffice') {
            abort(404);
        }

        // Get all permissions for backoffice guard
        $allPermissions = Permission::query()
            ->where('guard_name', 'backoffice')
            ->orderBy('name')
            ->get();

        // Get permissions already assigned to this role
        $rolePermissionIds = $role->permissions()->pluck('id')->toArray();

        // Build matrix grouped by module
        // Structure: $matrix[module][resource][action] = ['id' => ..., 'checked' => ...]
        $matrix = [];

        foreach ($allPermissions as $perm) {
            // Parse permission name: "module.resource.action"
            $parts = explode('.', $perm->name);

            if (count($parts) < 3) {
                // Skip malformed permissions
                continue;
            }

            $module = $parts[0];       // e.g., "agencies"
            $resource = $parts[1];     // e.g., "general"
            $action = $parts[2];       // e.g., "view", "create", "edit", "delete"

            // Initialize module if not exists
            if (!isset($matrix[$module])) {
                $matrix[$module] = [];
            }

            // Initialize resource if not exists
            if (!isset($matrix[$module][$resource])) {
                $matrix[$module][$resource] = [];
            }

            // Check if permission is assigned to role
            $isChecked = in_array($perm->id, $rolePermissionIds);

            // Store permission data
            $matrix[$module][$resource][$action] = [
                'id' => $perm->id,
                'name' => $perm->name,
                'checked' => $isChecked,
            ];
        }

        // Sort matrix by module
        ksort($matrix);

        // Sort each resource within module
        foreach ($matrix as &$resources) {
            ksort($resources);
        }

        return view('Backoffice.roles-permissions.permissions', compact('role', 'matrix', 'allPermissions'));
    }
    /**
     * Update permissions for a role
     * Expects permissions array in format: permissions[id] = 1
     */
    public function updatePermissions(Role $role, Request $request)
    {
        // Ensure the role is from backoffice guard
        if ($role->guard_name !== 'backoffice') {
            abort(404);
        }

        // Get array of permission IDs from request
        // Format: permissions = [id1 => 1, id2 => 1, ...]
        $permissionIds = array_keys($request->input('permissions', []) ?? []);

        // Validate that all permission IDs exist
        $validPermissions = Permission::query()
            ->where('guard_name', 'backoffice')
            ->whereIn('id', $permissionIds)
            ->pluck('id')
            ->toArray();

        // Sync permissions: remove old, add new (only valid ones)
        $role->syncPermissions($validPermissions);

        // Clear cached permissions after sync
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()
            ->route('backoffice.roles-permissions.permissions', $role->id)
            ->with('success', 'Permissions mises à jour avec succès.');
    }
}
