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
.umap-main-stack { display:flex; flex-direction:column; gap:18px; }
.umap-chart-body { padding:18px; overflow:auto; }
.umap-tree { list-style:none; margin:0; padding:0; min-width:520px; }
.umap-tree-item { position:relative; padding-left:28px; margin:0; }
.umap-tree-item::before { content:''; position:absolute; left:10px; top:0; bottom:0; width:1px; background:#e7e2e8; }
.umap-tree-item::after { content:''; position:absolute; left:10px; top:27px; width:18px; height:1px; background:#e7e2e8; }
.umap-tree > .umap-tree-item { padding-left:0; }
.umap-tree > .umap-tree-item::before,
.umap-tree > .umap-tree-item::after { display:none; }
.umap-tree-node { width:100%; display:flex; justify-content:space-between; align-items:center; gap:12px; padding:12px 14px; border:1px solid #e6e2e8; border-radius:14px; background:#fff; color:#121212; cursor:pointer; text-align:left; font-family:inherit; transition:all .16s ease; }
.umap-tree-node:hover { border-color:#fdba74; box-shadow:0 10px 20px rgba(254,95,4,.08); }
.umap-tree-node[disabled] { cursor:default; }
.umap-tree-main { display:flex; align-items:center; gap:10px; min-width:0; }
.umap-tree-toggle { width:24px; height:24px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; background:#fff7ed; color:#ea580c; font-weight:900; flex-shrink:0; }
.umap-tree-copy { min-width:0; }
.umap-tree-name { display:block; font-size:13px; font-weight:900; color:#121212; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.umap-tree-meta { display:block; margin-top:2px; font-size:11px; color:#7c7c7c; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.umap-tree-role { display:inline-flex; padding:4px 9px; border-radius:999px; background:#eef4ff; color:#3355aa; font-size:11px; font-weight:800; flex-shrink:0; }
.umap-tree-children { list-style:none; margin:10px 0 10px 18px; padding:0; display:none; }
.umap-tree-item.is-open > .umap-tree-children { display:block; }
.umap-tree-item.is-open > .umap-tree-node .umap-tree-toggle { background:#fe5f04; color:#fff; }
.umap-tree-empty { padding:34px 18px; text-align:center; color:#8a8a8a; font-size:13px; }
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

        <div class="umap-main-stack">
            <div class="umap-card">
                <div class="umap-card-head">
                    <div class="umap-card-title">User Hierarchy Chart</div>
                    <div class="umap-card-sub">Click any user to expand and see mapped users below them.</div>
                </div>
                <div class="umap-chart-body">
                    @if($userTree)
                        <ul class="umap-tree" data-user-tree>
                            @include('pages.auth_menu.mappings.partials.user-tree-node', ['node' => $userTree, 'isRoot' => true])
                        </ul>
                    @else
                        <div class="umap-tree-empty">Select a manager to view the hierarchy chart.</div>
                    @endif
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
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-tree-toggle]').forEach(function (button) {
        button.addEventListener('click', function () {
            const item = button.closest('.umap-tree-item');
            const hasChildren = button.getAttribute('data-has-children') === '1';

            if (!item || !hasChildren) {
                return;
            }

            item.classList.toggle('is-open');
            const toggle = button.querySelector('.umap-tree-toggle');

            if (toggle) {
                toggle.textContent = item.classList.contains('is-open') ? '-' : '+';
            }
        });
    });
});
</script>
@endpush
