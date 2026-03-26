<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    // ── ROLES ──────────────────────────────────────────────────

    /**
     * List all roles.
     */
    public function rolesIndex()
    {
        $this->authorize('manage roles');

        $roles = Role::withCount('users')->with('permissions')->get();

        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show create role form.
     */
    public function rolesCreate()
    {
        $this->authorize('manage roles');

        $permissions = Permission::all()->groupBy('module');

        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store new role.
     */
    public function rolesStore(Request $request)
    {
        $this->authorize('manage roles');

        $data = $request->validate([
            'name'         => ['required', 'string', 'max:100', 'unique:roles,name'],
            'display_name' => ['required', 'string', 'max:150'],
            'description'  => ['nullable', 'string', 'max:500'],
            'permissions'  => ['nullable', 'array'],
            'permissions.*'=> ['exists:permissions,name'],
        ]);

        $role = Role::create([
            'name'         => Str::slug($data['name'], '_'),
            'display_name' => $data['display_name'],
            'description'  => $data['description'] ?? null,
            'guard_name'   => 'web',
        ]);

        if (! empty($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$role->name}' created successfully.");
    }

    /**
     * Show edit role form.
     */
    public function rolesEdit(Role $role)
    {
        $this->authorize('manage roles');

        $permissions        = Permission::all()->groupBy('module');
        $rolePermissions    = $role->permissions->pluck('name')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update role.
     */
    public function rolesUpdate(Request $request, Role $role)
    {
        $this->authorize('manage roles');

        $data = $request->validate([
            'display_name' => ['required', 'string', 'max:150'],
            'description'  => ['nullable', 'string', 'max:500'],
            'permissions'  => ['nullable', 'array'],
            'permissions.*'=> ['exists:permissions,name'],
        ]);

        $role->update([
            'display_name' => $data['display_name'],
            'description'  => $data['description'] ?? null,
        ]);

        $role->syncPermissions($data['permissions'] ?? []);

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$role->name}' updated successfully.");
    }

    /**
     * Delete role.
     */
    public function rolesDestroy(Role $role)
    {
        $this->authorize('manage roles');

        if ($role->name === 'super_admin') {
            return back()->with('error', 'The super_admin role cannot be deleted.');
        }

        if ($role->users()->count() > 0) {
            return back()->with('error', "Cannot delete role with {$role->users()->count()} assigned user(s). Reassign users first.");
        }

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', "Role deleted successfully.");
    }

    // ── PERMISSIONS ────────────────────────────────────────────

    /**
     * List all permissions grouped by module.
     */
    public function permissionsIndex()
    {
        $this->authorize('manage roles');

        $permissions = Permission::all()->groupBy('module');

        return view('admin.permissions.index', compact('permissions'));
    }

    /**
     * Store new permission.
     */
    public function permissionsStore(Request $request)
    {
        $this->authorize('manage roles');

        $data = $request->validate([
            'name'         => ['required', 'string', 'max:100', 'unique:permissions,name'],
            'display_name' => ['required', 'string', 'max:150'],
            'module'       => ['required', 'string', 'max:50'],
            'description'  => ['nullable', 'string', 'max:500'],
        ]);

        Permission::create(array_merge($data, ['guard_name' => 'web']));

        return back()->with('success', "Permission '{$data['name']}' created.");
    }

    /**
     * Delete permission.
     */
    public function permissionsDestroy(Permission $permission)
    {
        $this->authorize('manage roles');

        $permission->delete();

        return back()->with('success', "Permission deleted.");
    }

    // ── USER ROLE ASSIGNMENT ───────────────────────────────────

    /**
     * Assign roles and permissions to a user.
     */
    public function assignUserRole(Request $request, User $user)
    {
        $this->authorize('manage roles');

        $data = $request->validate([
            'roles'       => ['nullable', 'array'],
            'roles.*'     => ['exists:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*'=> ['exists:permissions,name'],
        ]);

        // Sync roles
        $user->syncRoles($data['roles'] ?? []);

        // Sync direct permissions (extra permissions beyond role)
        $user->syncPermissions($data['permissions'] ?? []);

        return back()->with('success', "Roles and permissions updated for {$user->name}.");
    }
}