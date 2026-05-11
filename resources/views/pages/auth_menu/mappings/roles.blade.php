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
@endsection
