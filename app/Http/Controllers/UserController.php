<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * List all users with filters.
     */
    public function index(Request $request)
    {
        // $this->authorize('users.view');

        $query = User::with(['branch', 'roles'])
            ->when($request->search, fn($q) =>
                $q->where(fn($q2) =>
                    $q2->where('name', 'like', '%'.$request->search.'%')
                       ->orWhere('email', 'like', '%'.$request->search.'%')
                       ->orWhere('phone', 'like', '%'.$request->search.'%')
                )
            )
            ->when($request->branch_id, fn($q) =>
                $q->where('branch_id', $request->branch_id)
            )
            ->when($request->role, fn($q) =>
                $q->whereHas('roles', fn($q2) =>
                    $q2->where('name', $request->role)
                )
            )
            ->when($request->status !== null && $request->status !== '', fn($q) =>
                $q->where('is_active', $request->status)
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        $roles    = Role::orderBy('name')->get();

        return view('pages.users.index', compact('query', 'branches', 'roles'));
    }

    /**
     * Show create user form.
     */
    public function create()
    {
        // $this->authorize('users.manage');

        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        $roles    = Role::orderBy('name')->get();

        return view('pages.users.create', compact('branches', 'roles'));
    }

    /**
     * Store new user.
     */
    public function store(StoreUserRequest $request)
    {


        $data = $request->validated();

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $data['password'] = Hash::make($data['password']);
        unset($data['password_confirmation'], $data['role']);

        $user = User::create($data);

        // Assign role via Spatie
        if ($request->filled('role')) {
            $user->assignRole($request->role);
        }

        return redirect()
            ->route('users.index')
            ->with('success', "User <strong>{$user->name}</strong> created successfully.");
    }

    /**
     * Show user detail.
     */
    public function show(User $user)
    {
        // $this->authorize('users.view');

        $user->load(['branch', 'roles', 'roles.permissions']);

        return view('pages.users.show', compact('user'));
    }

    /**
     * Show edit form.
     */
    public function edit(User $user)
    {
        // $this->authorize('users.manage');

        $branches    = Branch::where('is_active', true)->orderBy('name')->get();
        $roles       = Role::orderBy('display_name')->get();
        $currentRole = $user->roles->first()?->name;

        return view('users.edit', compact('user', 'branches', 'roles', 'currentRole'));
    }

    /**
     * Update user.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        // $this->authorize('users.manage');

        $data = $request->validated();

        // Handle avatar
        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        // Only update password if provided
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        unset($data['password_confirmation'], $data['role']);

        $user->update($data);

        // Sync role
        if ($request->filled('role')) {
            $user->syncRoles([$request->role]);
        }

        return redirect()
            ->route('users.index')
            ->with('success', "User <strong>{$user->name}</strong> updated successfully.");
    }

    /**
     * Soft-delete user.
     */
    public function destroy(User $user)
    {
        // $this->authorize('users.manage');

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->hasRole('super_admin')) {
            return back()->with('error', 'Super admin accounts cannot be deleted.');
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', "User <strong>{$user->name}</strong> has been removed.");
    }

    /**
     * Toggle active/inactive status.
     */
    public function toggleStatus(User $user)
    {
        // $this->authorize('users.manage');

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "User <strong>{$user->name}</strong> has been {$status}.");
    }

    /**
     * Reset user password (admin action).
     */
    public function resetPassword(Request $request, User $user)
    {
        // $this->authorize('users.manage');

        $request->validate([
            'new_password'              => ['required', 'string', 'min:8', 'confirmed'],
            'new_password_confirmation' => ['required'],
        ]);

        $user->update(['password' => Hash::make($request->new_password)]);

        return back()->with('success', "Password for <strong>{$user->name}</strong> has been reset.");
    }
}
