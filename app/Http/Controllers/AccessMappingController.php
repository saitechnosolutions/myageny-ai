<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\RoleMapping;
use App\Models\User;
use App\Models\UserMapping;
use App\Services\DataVisibilityService;
use Illuminate\Support\Collection;
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
        $roleChart = $this->buildRoleChart();

        $accessLevels = RoleMapping::ACCESS_LEVELS;

        return view('pages.auth_menu.mappings.roles', compact('roles', 'accessLevels', 'roleChart'));
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
        $selectedManager = $users->firstWhere('id', $selectedManagerId);
        $userTree = $selectedManager
            ? $this->buildUserTree($selectedManager, $users, collect([$selectedManager->id]))
            : null;

        return view('pages.auth_menu.mappings.users', compact(
            'users',
            'selectedManagerId',
            'selectedUserIds',
            'mappings',
            'selectedManager',
            'userTree'
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

    private function buildRoleChart(): Collection
    {
        $mappings = UserMapping::with(['manager.roles', 'user.roles'])
            ->orderBy('manager_id')
            ->get();

        return $mappings
            ->groupBy(fn (UserMapping $mapping) => $this->roleLabel($mapping->manager))
            ->map(function (Collection $managerMappings, string $managerRole) {
                $managers = $managerMappings
                    ->pluck('manager')
                    ->filter()
                    ->unique('id')
                    ->sortBy('name')
                    ->map(fn (User $user) => $this->userChartPayload($user))
                    ->values();

                $childRoles = $managerMappings
                    ->groupBy(fn (UserMapping $mapping) => $this->roleLabel($mapping->user))
                    ->map(function (Collection $childMappings, string $childRole) {
                        $users = $childMappings
                            ->pluck('user')
                            ->filter()
                            ->unique('id')
                            ->sortBy('name')
                            ->map(fn (User $user) => $this->userChartPayload($user))
                            ->values();

                        return [
                            'role' => $childRole,
                            'users' => $users,
                            'count' => $users->count(),
                        ];
                    })
                    ->sortBy('role')
                    ->values();

                return [
                    'role' => $managerRole,
                    'managers' => $managers,
                    'children' => $childRoles,
                    'count' => $childRoles->sum('count'),
                ];
            })
            ->sortBy('role')
            ->values();
    }

    private function buildUserTree(User $user, Collection $users, Collection $visited): array
    {
        $children = UserMapping::query()
            ->where('manager_id', $user->id)
            ->pluck('user_id')
            ->map(fn ($id) => (int) $id)
            ->reject(fn (int $userId) => $visited->contains($userId))
            ->values();

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $this->roleLabel($user),
            'children' => $children
                ->map(function (int $userId) use ($users, $visited) {
                    $child = $users->firstWhere('id', $userId);

                    if (! $child) {
                        return null;
                    }

                    return $this->buildUserTree($child, $users, $visited->merge([$userId])->unique()->values());
                })
                ->filter()
                ->values()
                ->all(),
        ];
    }

    private function roleLabel(?User $user): string
    {
        if (! $user) {
            return 'No Role';
        }

        $role = $user->roles->first();

        if (! $role) {
            return 'No Role';
        }

        return $role->display_name ?: str($role->name)->after('__')->replace('_', ' ')->title()->value();
    }

    private function userChartPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $this->roleLabel($user),
        ];
    }
}