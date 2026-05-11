<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\RoleMapping;
use App\Models\User;
use App\Models\UserMapping;
use App\Services\DataVisibilityService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AccessMappingController extends Controller
{
    public function __construct(private readonly DataVisibilityService $visibility) {}

    public function roleIndex()
    {
        $roles = Role::with(['roleMapping', 'users'])
            ->orderByRaw('COALESCE(display_name, name)')
            ->get();

        $accessLevels = RoleMapping::ACCESS_LEVELS;

        return view('pages.auth_menu.mappings.roles', compact('roles', 'accessLevels'));
    }

    public function roleUpdate(Request $request)
    {
        $data = $request->validate([
            'mappings' => ['required', 'array'],
            'mappings.*' => ['required', Rule::in(array_keys(RoleMapping::ACCESS_LEVELS))],
        ]);

        $roles = Role::whereIn('id', array_keys($data['mappings']))->get();
        $companyId = auth()->user()?->company_id;

        foreach ($roles as $role) {
            RoleMapping::updateOrCreate(
                ['role_id' => $role->id],
                [
                    'company_id' => $role->company_id ?: $companyId,
                    'access_level' => $data['mappings'][$role->id],
                ]
            );
        }

        return back()->with('success', 'Role mapping updated successfully.');
    }

    public function userIndex(Request $request)
    {
        $users = User::with('roles')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $selectedManagerId = (int) ($request->input('manager_id') ?: $users->first()?->id);
        $selectedUserIds = $selectedManagerId
            ? UserMapping::where('manager_id', $selectedManagerId)->pluck('user_id')->map(fn ($id) => (int) $id)->all()
            : [];

        $mappings = UserMapping::with(['manager.roles', 'user.roles'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('pages.auth_menu.mappings.users', compact(
            'users',
            'selectedManagerId',
            'selectedUserIds',
            'mappings'
        ));
    }

    public function userUpdate(Request $request)
    {
        $data = $request->validate([
            'manager_id' => ['required', 'exists:users,id'],
            'user_ids' => ['nullable', 'array'],
            'user_ids.*' => ['integer', 'distinct', 'exists:users,id'],
        ]);

        $manager = User::findOrFail($data['manager_id']);
        $userIds = collect($data['user_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->reject(fn (int $id) => $id === (int) $manager->id)
            ->unique()
            ->values();

        if (auth()->user()?->company_id !== null) {
            $idsToCheck = $userIds->merge([$manager->id])->unique();

            $invalidCompanyUser = User::whereIn('id', $idsToCheck)
                ->where('company_id', '!=', auth()->user()->company_id)
                ->exists();

            abort_if($invalidCompanyUser, 403);
        }

        foreach ($userIds as $userId) {
            $candidate = User::find($userId);

            if ($candidate && $this->visibility->descendantUserIds($candidate)->contains($manager->id)) {
                return back()
                    ->withInput()
                    ->with('error', 'This mapping would create a reporting loop. Please choose another user.');
            }
        }

        UserMapping::where('manager_id', $manager->id)
            ->whereNotIn('user_id', $userIds)
            ->delete();

        foreach ($userIds as $userId) {
            UserMapping::updateOrCreate(
                ['user_id' => $userId],
                [
                    'manager_id' => $manager->id,
                    'company_id' => $manager->company_id ?: auth()->user()?->company_id,
                ]
            );
        }

        return redirect()
            ->route('auth.user-mappings.index', ['manager_id' => $manager->id])
            ->with('success', 'User mapping updated successfully.');
    }

    public function userDestroy(UserMapping $mapping)
    {
        $mapping->delete();

        return back()->with('success', 'User mapping removed successfully.');
    }
}
