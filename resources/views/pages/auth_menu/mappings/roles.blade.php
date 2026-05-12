@extends('layouts.app')

@section('title', 'Role Mapping')

@push('styles')
<style>
.map-page { min-height:100%; padding:28px; background:#f4f5f7; }
.map-topbar { display:flex; align-items:flex-end; justify-content:space-between; gap:16px; margin-bottom:18px; }
.map-title { margin:0; font-size:22px; font-weight:800; color:#121212; }
.map-subtitle { margin-top:4px; font-size:13px; color:#7c7c7c; }
.map-actions { display:flex; gap:10px; align-items:center; }
.map-btn { display:inline-flex; align-items:center; justify-content:center; gap:8px; min-height:38px; padding:9px 15px; border-radius:10px; border:1px solid #e1dee3; background:#fff; color:#121212; text-decoration:none; font-size:13px; font-weight:700; cursor:pointer; }
.map-btn-primary { border-color:#fe5f04; background:linear-gradient(135deg,#fe5f04,#ff7c30); color:#fff; }
.map-alert { margin-bottom:14px; padding:12px 14px; border-radius:10px; font-size:13px; }
.map-alert.success { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; }
.map-alert.error { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; }
.map-card { background:#fff; border:1px solid #e1dee3; border-radius:16px; overflow:hidden; }
.map-table { width:100%; border-collapse:collapse; }
.map-table th, .map-table td { padding:14px 16px; border-bottom:1px solid #f1eff3; text-align:left; font-size:13px; vertical-align:middle; }
.map-table th { background:#fafafa; color:#8a8a8a; font-size:11px; font-weight:800; letter-spacing:.6px; text-transform:uppercase; }
.map-table tr:last-child td { border-bottom:none; }
.map-role { font-weight:800; color:#121212; }
.map-muted { color:#7c7c7c; font-size:12px; }
.map-badge { display:inline-flex; padding:4px 9px; border-radius:999px; background:#fff7ed; color:#ea580c; border:1px solid #fed7aa; font-size:11px; font-weight:800; }
.map-select { width:100%; min-width:190px; height:38px; padding:0 11px; border:1px solid #e1dee3; border-radius:10px; background:#fff; font-size:13px; color:#121212; }
.map-section-stack { display:flex; flex-direction:column; gap:18px; }
.map-card-head { padding:16px 18px; border-bottom:1px solid #f1eff3; display:flex; justify-content:space-between; gap:14px; align-items:flex-start; }
.map-card-title { font-size:15px; font-weight:800; color:#121212; }
.map-card-sub { margin-top:3px; font-size:12px; color:#7c7c7c; line-height:1.5; }
.map-chart-body { padding:18px; overflow-x:auto; }
.map-role-chart { display:flex; flex-direction:column; gap:14px; min-width:720px; }
.map-role-node { border:1px solid #e7e4e8; border-radius:16px; background:#fff; overflow:hidden; }
.map-role-node summary { list-style:none; cursor:pointer; }
.map-role-node summary::-webkit-details-marker { display:none; }
.map-role-root { display:flex; justify-content:space-between; align-items:flex-start; gap:14px; padding:16px 18px; background:linear-gradient(135deg,#fff7ed,#fff); border-bottom:1px solid #f2ece7; }
.map-role-root-name { font-size:15px; font-weight:900; color:#121212; }
.map-role-root-meta { margin-top:5px; display:flex; gap:8px; flex-wrap:wrap; }
.map-role-count { display:inline-flex; padding:4px 9px; border-radius:999px; background:#eef4ff; color:#3355aa; font-size:11px; font-weight:800; }
.map-manager-list { display:flex; gap:8px; flex-wrap:wrap; max-width:520px; justify-content:flex-end; }
.map-user-pill { display:inline-flex; flex-direction:column; gap:2px; padding:7px 10px; border:1px solid #e9e4df; border-radius:12px; background:#fff; }
.map-user-pill strong { font-size:12px; color:#121212; }
.map-user-pill span { font-size:10px; color:#8a8a8a; }
.map-child-grid { padding:16px 18px 18px; display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:12px; background:#fcfcfd; }
.map-child-role { border:1px solid #ece8ee; border-radius:14px; background:#fff; padding:14px; }
.map-child-role-head { display:flex; justify-content:space-between; gap:10px; align-items:center; margin-bottom:10px; }
.map-child-role-title { font-size:13px; font-weight:900; color:#121212; }
.map-child-users { display:flex; flex-direction:column; gap:8px; }
.map-empty-box { padding:32px 18px; text-align:center; color:#8a8a8a; font-size:13px; }
@media (max-width: 800px) {
    .map-page { padding:18px; }
    .map-topbar { flex-direction:column; align-items:flex-start; }
    .map-card { overflow-x:auto; }
}
</style>
@endpush

@section('content')
<div class="map-page">
    <div class="map-topbar">
        <div>
            <h2 class="map-title">Role Mapping</h2>
            <div class="map-subtitle">Set each role to company, mapped team, or assigned-only data access.</div>
        </div>
        <div class="map-actions">
            <a href="{{ route('auth.index') }}" class="map-btn">Back</a>
            <button type="submit" form="roleMappingForm" class="map-btn map-btn-primary">Save Mapping</button>
        </div>
    </div>

    @if(session('success'))
        <div class="map-alert success">{!! session('success') !!}</div>
    @endif
    @if(session('error'))
        <div class="map-alert error">{!! session('error') !!}</div>
    @endif

    <div class="map-section-stack">
        <div class="map-card">
            <div class="map-card-head">
                <div>
                    <div class="map-card-title">Role Hierarchy Chart</div>
                    <div class="map-card-sub">Shows each manager role and the mapped user roles below it from User Mapping.</div>
                </div>
                <span class="map-role-count">{{ $roleChart->count() }} parent role{{ $roleChart->count() === 1 ? '' : 's' }}</span>
            </div>

            <div class="map-chart-body">
                @if($roleChart->isEmpty())
                    <div class="map-empty-box">No user mappings found yet. Map users under managers to build the role chart.</div>
                @else
                    <div class="map-role-chart">
                        @foreach($roleChart as $roleNode)
                            <details class="map-role-node" {{ $loop->first ? 'open' : '' }}>
                                <summary>
                                    <div class="map-role-root">
                                        <div>
                                            <div class="map-role-root-name">{{ $roleNode['role'] }}</div>
                                            <div class="map-role-root-meta">
                                                <span class="map-role-count">{{ $roleNode['managers']->count() }} manager{{ $roleNode['managers']->count() === 1 ? '' : 's' }}</span>
                                                <span class="map-role-count">{{ $roleNode['count'] }} mapped user{{ $roleNode['count'] === 1 ? '' : 's' }}</span>
                                            </div>
                                        </div>
                                        <div class="map-manager-list">
                                            @foreach($roleNode['managers'] as $manager)
                                                <span class="map-user-pill">
                                                    <strong>{{ $manager['name'] }}</strong>
                                                    <span>{{ $manager['email'] }}</span>
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </summary>

                                <div class="map-child-grid">
                                    @foreach($roleNode['children'] as $childRole)
                                        <div class="map-child-role">
                                            <div class="map-child-role-head">
                                                <div class="map-child-role-title">{{ $childRole['role'] }}</div>
                                                <span class="map-role-count">{{ $childRole['count'] }}</span>
                                            </div>
                                            <div class="map-child-users">
                                                @foreach($childRole['users'] as $user)
                                                    <span class="map-user-pill">
                                                        <strong>{{ $user['name'] }}</strong>
                                                        <span>{{ $user['email'] }}</span>
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </details>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <form id="roleMappingForm" method="POST" action="{{ route('auth.role-mappings.update') }}">
            @csrf
            @method('PUT')

            <div class="map-card">
                <table class="map-table">
                    <thead>
                        <tr>
                            <th>Role</th>
                            <th>Role Key</th>
                            <th>Users</th>
                            <th>Current Mapping</th>
                            <th>Access Level</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                            @php
                                $inferred = app(\App\Services\DataVisibilityService::class)->defaultAccessLevelForRole($role);
                                $selected = old("mappings.{$role->id}", $role->roleMapping?->access_level ?? $inferred);
                            @endphp
                            <tr>
                                <td>
                                    <div class="map-role">{{ $role->display_name ?: ucfirst(str_replace('_', ' ', $role->name)) }}</div>
                                    <div class="map-muted">{{ $role->description ?: 'No description' }}</div>
                                </td>
                                <td><span class="map-badge">{{ $role->name }}</span></td>
                                <td>{{ $role->users->count() }}</td>
                                <td class="map-muted">{{ \App\Models\RoleMapping::labelFor($role->roleMapping?->access_level ?? $inferred) }}</td>
                                <td>
                                    <select class="map-select" name="mappings[{{ $role->id }}]">
                                        @foreach($accessLevels as $value => $label)
                                            <option value="{{ $value }}" @selected($selected === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error("mappings.{$role->id}")<div class="map-muted">{{ $message }}</div>@enderror
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="map-muted">No roles found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>
@endsection
