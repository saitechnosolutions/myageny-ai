@extends('layouts.app')

@section('title', 'User Mapping')

@push('styles')
<style>
.umap-page { min-height:100%; padding:28px; background:#f4f5f7; }
.umap-topbar { display:flex; align-items:flex-end; justify-content:space-between; gap:16px; margin-bottom:18px; }
.umap-title { margin:0; font-size:22px; font-weight:800; color:#121212; }
.umap-subtitle { margin-top:4px; font-size:13px; color:#7c7c7c; }
.umap-actions { display:flex; gap:10px; align-items:center; }
.umap-btn { display:inline-flex; align-items:center; justify-content:center; gap:8px; min-height:38px; padding:9px 15px; border-radius:10px; border:1px solid #e1dee3; background:#fff; color:#121212; text-decoration:none; font-size:13px; font-weight:700; cursor:pointer; }
.umap-btn-primary { border-color:#fe5f04; background:linear-gradient(135deg,#fe5f04,#ff7c30); color:#fff; }
.umap-alert { margin-bottom:14px; padding:12px 14px; border-radius:10px; font-size:13px; }
.umap-alert.success { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; }
.umap-alert.error { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; }
.umap-grid { display:grid; grid-template-columns:minmax(280px, 420px) 1fr; gap:18px; align-items:start; }
.umap-card { background:#fff; border:1px solid #e1dee3; border-radius:16px; overflow:hidden; }
.umap-card-head { padding:16px 18px; border-bottom:1px solid #f1eff3; }
.umap-card-title { font-size:15px; font-weight:800; color:#121212; }
.umap-card-sub { margin-top:3px; font-size:12px; color:#7c7c7c; }
.umap-body { padding:18px; }
.umap-field { margin-bottom:14px; }
.umap-label { display:block; margin-bottom:6px; font-size:12px; font-weight:800; color:#555; }
.umap-select { width:100%; min-height:38px; padding:8px 11px; border:1px solid #e1dee3; border-radius:10px; background:#fff; font-size:13px; color:#121212; }
.umap-checks { display:grid; gap:8px; max-height:440px; overflow:auto; padding-right:4px; }
.umap-check { display:flex; align-items:center; gap:10px; padding:10px; border:1px solid #eee; border-radius:10px; background:#fafafa; }
.umap-check input { width:16px; height:16px; }
.umap-user { font-weight:800; color:#121212; font-size:13px; }
.umap-role { color:#7c7c7c; font-size:11px; margin-top:2px; }
.umap-table { width:100%; border-collapse:collapse; }
.umap-table th, .umap-table td { padding:13px 15px; border-bottom:1px solid #f1eff3; text-align:left; font-size:13px; vertical-align:middle; }
.umap-table th { background:#fafafa; color:#8a8a8a; font-size:11px; font-weight:800; letter-spacing:.6px; text-transform:uppercase; }
.umap-table tr:last-child td { border-bottom:none; }
.umap-muted { color:#7c7c7c; font-size:12px; }
.umap-delete { border:none; background:#fef2f2; color:#dc2626; border-radius:9px; width:32px; height:32px; cursor:pointer; font-weight:800; }
@media (max-width: 1000px) {
    .umap-page { padding:18px; }
    .umap-grid { grid-template-columns:1fr; }
    .umap-topbar { flex-direction:column; align-items:flex-start; }
}
</style>
@endpush

@section('content')
<div class="umap-page">
    <div class="umap-topbar">
        <div>
            <h2 class="umap-title">User Mapping</h2>
            <div class="umap-subtitle">Map users under TLs or managers for team-level CRM visibility.</div>
        </div>
        <div class="umap-actions">
            <a href="{{ route('auth.index') }}" class="umap-btn">Back</a>
            <button type="submit" form="userMappingForm" class="umap-btn umap-btn-primary">Save Mapping</button>
        </div>
    </div>

    @if(session('success'))
        <div class="umap-alert success">{!! session('success') !!}</div>
    @endif
    @if(session('error'))
        <div class="umap-alert error">{!! session('error') !!}</div>
    @endif

    <div class="umap-grid">
        <div class="umap-card">
            <div class="umap-card-head">
                <div class="umap-card-title">Manager Users</div>
                <div class="umap-card-sub">Choose a manager and select the users under them.</div>
            </div>
            <div class="umap-body">
                <form method="GET" action="{{ route('auth.user-mappings.index') }}" class="umap-field">
                    <label class="umap-label" for="managerFilter">Manager / TL</label>
                    <select id="managerFilter" name="manager_id" class="umap-select" onchange="this.form.submit()">
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" @selected($selectedManagerId === $user->id)>
                                {{ $user->name }} - {{ $user->roles->first()?->display_name ?? $user->roles->first()?->name ?? 'No Role' }}
                            </option>
                        @endforeach
                    </select>
                </form>

                <form id="userMappingForm" method="POST" action="{{ route('auth.user-mappings.update') }}">
                    @csrf
                    <input type="hidden" name="manager_id" value="{{ $selectedManagerId }}">

                    <div class="umap-checks">
                        @foreach($users as $user)
                            @continue($user->id === $selectedManagerId)
                            <label class="umap-check">
                                <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" @checked(in_array($user->id, $selectedUserIds, true))>
                                <span>
                                    <span class="umap-user">{{ $user->name }}</span>
                                    <span class="umap-role">{{ $user->roles->first()?->display_name ?? $user->roles->first()?->name ?? 'No Role' }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </form>
            </div>
        </div>

        <div class="umap-card">
            <div class="umap-card-head">
                <div class="umap-card-title">Current Mapping</div>
                <div class="umap-card-sub">{{ $mappings->total() }} mapped user{{ $mappings->total() === 1 ? '' : 's' }}</div>
            </div>

            <div style="overflow-x:auto;">
                <table class="umap-table">
                    <thead>
                        <tr>
                            <th>Manager / TL</th>
                            <th>User</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mappings as $mapping)
                            <tr>
                                <td>
                                    <strong>{{ $mapping->manager?->name }}</strong>
                                    <div class="umap-muted">{{ $mapping->manager?->email }}</div>
                                </td>
                                <td>
                                    <strong>{{ $mapping->user?->name }}</strong>
                                    <div class="umap-muted">{{ $mapping->user?->email }}</div>
                                </td>
                                <td class="umap-muted">{{ $mapping->user?->roles->first()?->display_name ?? $mapping->user?->roles->first()?->name ?? 'No Role' }}</td>
                                <td>
                                    <form method="POST" action="{{ route('auth.user-mappings.destroy', $mapping) }}" onsubmit="return confirm('Remove this mapping?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="umap-delete" type="submit" title="Remove">x</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="umap-muted">No user mappings found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($mappings->hasPages())
                @include('partials.table-pagination', ['paginator' => $mappings])
            @endif
        </div>
    </div>
</div>
@endsection
