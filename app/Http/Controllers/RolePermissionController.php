<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    private const DEFAULT_PERMISSION_ACTIONS = [
        'create',
        'edit',
        'view',
        'delete',
        'update',
        'approve',
        'reject',
    ];

    // ── ROLES ──────────────────────────────────────────────────

    /**
     * List all roles.
     */
    public function rolesIndex()
    {
        $roles = Role::withCount('users')
            ->with('permissions')
            ->orderByRaw('COALESCE(display_name, name)')
            ->paginate(10)
            ->withQueryString();

        return view('pages.auth_menu.roles.index', compact('roles'));
    }

    /**
     * Show create role form.
     */
    public function rolesCreate()
    {
        $permissions = Permission::orderBy('module')
            ->orderByRaw('COALESCE(display_name, name)')
            ->get()
            ->groupBy(fn (Permission $permission) => $permission->module ?: 'general');

        return view('pages.auth_menu.roles.create', compact('permissions'));
    }

    /**
     * Store new role.
     */
    public function rolesStore(Request $request)
    {
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

        return redirect()->route('auth.roles.index')
            ->with('success', "Role '{$role->name}' created successfully.");
    }

    /**
     * Show edit role form.
     */
    public function rolesEdit(Role $role)
    {
        return view('pages.auth_menu.roles.edit', compact('role'));
    }

    /**
     * Update role.
     */
    public function rolesUpdate(Request $request, Role $role)
    {
        $data = $request->validate([
            'display_name' => ['required', 'string', 'max:150'],
            'description'  => ['nullable', 'string', 'max:500'],
        ]);

        $role->update([
            'display_name' => $data['display_name'],
            'description'  => $data['description'] ?? null,
        ]);
        return redirect()->route('auth.roles.index')
            ->with('success', "Role '{$role->name}' updated successfully.");
    }

    public function rolesPermissionsEdit(Role $role)
    {
        $permissions = Permission::orderBy('module')
            ->orderByRaw('COALESCE(display_name, name)')
            ->get()
            ->groupBy(fn (Permission $permission) => $permission->module ?: 'general');
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('pages.auth_menu.roles.permissions', compact('role', 'permissions', 'rolePermissions'));
    }

    public function rolesPermissionsUpdate(Request $request, Role $role)
    {
        $data = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $role->syncPermissions($data['permissions'] ?? []);

        $roleLabel = $role->display_name ?: $role->name;

        return redirect()
            ->route('auth.roles.permissions.edit', $role)
            ->with('success', "Permissions updated for role '{$roleLabel}'.");
    }

    /**
     * Delete role.
     */
    public function rolesDestroy(Role $role)
    {
        if (strtolower(str_replace(' ', '_', $role->name)) === 'super_admin') {
            return back()->with('error', 'The super_admin role cannot be deleted.');
        }

        if ($role->users()->count() > 0) {
            return back()->with('error', "Cannot delete role with {$role->users()->count()} assigned user(s). Reassign users first.");
        }

        $role->delete();

        return redirect()->route('auth.roles.index')
            ->with('success', "Role deleted successfully.");
    }

    // ── PERMISSIONS ────────────────────────────────────────────

    /**
     * List all permissions grouped by module.
     */
    public function permissionsIndex()
    {
        $permissionPages = Permission::orderBy('module')
            ->orderByRaw('COALESCE(display_name, name)')
            ->paginate(15)
            ->withQueryString();
        $permissions = $permissionPages->getCollection()
            ->groupBy(fn (Permission $permission) => $permission->module ?: 'general');

        return view('pages.auth_menu.permissions.index', compact('permissions', 'permissionPages'));
    }

    /**
     * Show create permission form.
     */
    public function permissionsCreate()
    {
        $defaultActions = self::DEFAULT_PERMISSION_ACTIONS;

        return view('pages.auth_menu.permissions.create', compact('defaultActions'));
    }

    /**
     * Store new permission.
     */
    public function permissionsStore(Request $request)
    {
        $data = $request->validate([
            'module'       => ['required', 'string', 'max:50'],
            'actions'      => ['nullable', 'array'],
            'actions.*'    => ['string', 'in:' . implode(',', self::DEFAULT_PERMISSION_ACTIONS)],
            'custom_actions' => ['nullable', 'string', 'max:500'],
            'description'  => ['nullable', 'string', 'max:500'],
        ]);

        $moduleKey = Str::slug($data['module'], '_');
        $moduleName = Str::title(str_replace('_', ' ', $moduleKey));
        $customActions = collect(preg_split('/[\s,]+/', (string) ($data['custom_actions'] ?? ''), -1, PREG_SPLIT_NO_EMPTY))
            ->map(fn (string $action) => Str::slug($action, '_'))
            ->filter()
            ->values()
            ->all();
        $actions = collect($data['actions'] ?? [])
            ->merge($customActions)
            ->unique()
            ->values();

        if ($actions->isEmpty()) {
            return back()
                ->withErrors(['actions' => 'Select at least one default action or add a custom action.'])
                ->withInput();
        }

        $createdPermissions = [];
        $skippedPermissions = [];

        foreach ($actions as $action) {
            $permissionName = $moduleKey . '.' . $action;

            $permission = Permission::firstOrCreate(
                ['name' => $permissionName, 'guard_name' => 'web'],
                [
                    'display_name' => Str::title($action . ' ' . $moduleName),
                    'module' => $moduleKey,
                    'description' => $data['description'] ?: ('Allows users to ' . $action . ' ' . strtolower($moduleName) . '.'),
                ]
            );

            if ($permission->wasRecentlyCreated) {
                $createdPermissions[] = $permissionName;
            } else {
                $skippedPermissions[] = $permissionName;
            }
        }

        $message = count($createdPermissions) . " permission(s) created for module '{$moduleName}'.";

        if ($skippedPermissions !== []) {
            $message .= ' Skipped existing: ' . implode(', ', $skippedPermissions) . '.';
        }

        return redirect()
            ->route('auth.permissions.index')
            ->with('success', $message);
    }

    /**
     * Show edit permission form.
     */
    public function permissionsEdit(Permission $permission)
    {
        return view('pages.auth_menu.permissions.edit', compact('permission'));
    }

    /**
     * Update permission.
     */
    public function permissionsUpdate(Request $request, Permission $permission)
    {
        $data = $request->validate([
            'display_name' => ['required', 'string', 'max:150'],
            'module'       => ['required', 'string', 'max:50'],
            'description'  => ['nullable', 'string', 'max:500'],
        ]);

        $permission->update($data);

        return redirect()
            ->route('auth.permissions.index')
            ->with('success', "Permission '{$permission->name}' updated.");
    }

    /**
     * Delete permission.
     */
    public function permissionsDestroy(Permission $permission)
    {
        $permission->delete();

        return back()->with('success', "Permission deleted.");
    }

    // ── USER ROLE ASSIGNMENT ───────────────────────────────────

    /**
     * Assign roles and permissions to a user.
     */
    public function assignUserRole(Request $request, User $user)
    {
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
