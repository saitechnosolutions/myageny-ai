@extends('layouts.app')

@section('title', 'Assign Role Permissions')

@push('styles')
<style>
.rperm-page { padding: 28px; background: #f4f5f7; min-height: 100%; }
.rperm-topbar { display:flex; justify-content:space-between; align-items:center; gap:16px; margin-bottom:20px; }
.rperm-title { font-size:22px; font-weight:800; color:#121212; }
.rperm-sub { font-size:13px; color:#7c7c7c; margin-top:4px; }
.rperm-btn { display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:10px 16px; border-radius:10px; text-decoration:none; border:1px solid #e1dee3; font-size:13px; font-weight:700; background:#fff; color:#222; cursor:pointer; }
.rperm-btn-primary { background:linear-gradient(135deg,#fe5f04,#ff7c30); color:#fff; border:none; }
.rperm-card { background:#fff; border:1px solid #e1dee3; border-radius:16px; overflow:hidden; }
.rperm-card-head { display:flex; justify-content:space-between; align-items:center; gap:12px; padding:16px 20px; border-bottom:1px solid #f1eff3; }
.rperm-card-title { font-size:15px; font-weight:800; color:#121212; }
.rperm-card-sub { font-size:12px; color:#8a8a8a; margin-top:4px; }
.rperm-toolbar { display:flex; gap:10px; flex-wrap:wrap; }
.rperm-scroll { padding:20px; display:flex; flex-direction:column; gap:16px; }
.rperm-module { border:1px solid #f0eef2; border-radius:14px; overflow:hidden; }
.rperm-module-head { display:flex; justify-content:space-between; align-items:center; gap:12px; padding:14px 16px; background:#fafafa; border-bottom:1px solid #f1eff3; }
.rperm-module-title { font-size:12px; font-weight:800; color:#555; letter-spacing:.8px; }
.rperm-module-actions { display:flex; gap:8px; }
.rperm-link-btn { border:none; background:none; color:#2563eb; font-size:12px; font-weight:700; cursor:pointer; padding:0; }
.rperm-list { padding:16px; display:grid; grid-template-columns:repeat(auto-fit, minmax(260px, 1fr)); gap:10px; }
.rperm-check { display:flex; gap:10px; align-items:flex-start; border:1px solid #f1eff3; border-radius:12px; padding:12px; background:#fff; cursor:pointer; }
.rperm-check input { margin-top:3px; }
.rperm-check strong { display:block; font-size:13px; color:#121212; }
.rperm-check small { display:block; font-size:11px; color:#8a8a8a; margin-top:3px; }
.rperm-flash { margin-bottom:16px; padding:12px 14px; border-radius:10px; font-size:13px; }
.rperm-flash.success { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; }
.rperm-flash.error { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; }
.rperm-empty { color:#8a8a8a; font-size:13px; }
</style>
@endpush

@section('content')
<div class="rperm-page">
    <div class="rperm-topbar">
        <div>
            <div class="rperm-title">Assign Permissions</div>
            <div class="rperm-sub">Manage access for <strong>{{ $role->display_name ?: ucfirst(str_replace('_', ' ', $role->name)) }}</strong> module by module.</div>
        </div>
        <div style="display:flex;gap:10px;">
            <a href="{{ route('auth.roles.index') }}" class="rperm-btn">Back</a>
            <a href="{{ route('auth.roles.edit', $role) }}" class="rperm-btn">Edit Role</a>
        </div>
    </div>

    @if(session('success'))
        <div class="rperm-flash success">{!! session('success') !!}</div>
    @endif
    @if(session('error'))
        <div class="rperm-flash error">{!! session('error') !!}</div>
    @endif

    <form method="POST" action="{{ route('auth.roles.permissions.update', $role) }}">
        @csrf
        @method('PUT')

        <div class="rperm-card">
            <div class="rperm-card-head">
                <div>
                    <div class="rperm-card-title">Role Access Matrix</div>
                    <div class="rperm-card-sub">Select individual permissions, a full module, or everything at once.</div>
                </div>
                <div class="rperm-toolbar">
                    <button type="button" class="rperm-btn" onclick="toggleAllPermissions(true)">Select All</button>
                    <button type="button" class="rperm-btn" onclick="toggleAllPermissions(false)">Clear All</button>
                    <button type="submit" class="rperm-btn rperm-btn-primary">Save Permissions</button>
                </div>
            </div>

            <div class="rperm-scroll">
                @php $selectedPermissions = collect(old('permissions', $rolePermissions ?? [])); @endphp
                @forelse($permissions as $module => $items)
                    <div class="rperm-module">
                        <div class="rperm-module-head">
                            <div class="rperm-module-title">{{ strtoupper($module) }}</div>
                            <div class="rperm-module-actions">
                                <button type="button" class="rperm-link-btn" onclick="toggleModulePermissions('{{ $module }}', true)">Select All</button>
                                <button type="button" class="rperm-link-btn" onclick="toggleModulePermissions('{{ $module }}', false)">Clear</button>
                            </div>
                        </div>
                        <div class="rperm-list">
                            @foreach($items as $permission)
                                <label class="rperm-check">
                                    <input type="checkbox"
                                           name="permissions[]"
                                           value="{{ $permission->name }}"
                                           data-permission-checkbox="true"
                                           data-module-name="{{ $module }}"
                                           {{ $selectedPermissions->contains($permission->name) ? 'checked' : '' }}>
                                    <span>
                                        <strong>{{ $permission->display_name ?: ucfirst(str_replace(['.', '_'], ' ', $permission->name)) }}</strong>
                                        <small>{{ $permission->description ?: $permission->name }}</small>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="rperm-empty">No permissions found. Create permissions first.</div>
                @endforelse

                @error('permissions')<div class="rperm-flash error">{{ $message }}</div>@enderror
                @error('permissions.*')<div class="rperm-flash error">{{ $message }}</div>@enderror
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function toggleAllPermissions(checked) {
    document.querySelectorAll('[data-permission-checkbox="true"]').forEach(function (checkbox) {
        checkbox.checked = checked;
    });
}

function toggleModulePermissions(moduleName, checked) {
    document.querySelectorAll('[data-module-name="' + moduleName + '"]').forEach(function (checkbox) {
        checkbox.checked = checked;
    });
}
</script>
@endpush
