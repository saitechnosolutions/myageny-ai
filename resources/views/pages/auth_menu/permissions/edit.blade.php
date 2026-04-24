@extends('layouts.app')

@section('title', 'Edit Permission')

@push('styles')
<style>
.perm-editor { padding:28px; background:#f4f5f7; min-height:100%; }
.perm-editor-top { display:flex; justify-content:space-between; align-items:center; gap:16px; margin-bottom:20px; }
.perm-editor-title { font-size:22px; font-weight:800; color:#121212; }
.perm-editor-sub { font-size:13px; color:#7c7c7c; margin-top:4px; }
.perm-form-card { background:#fff; border:1px solid #e1dee3; border-radius:16px; padding:20px; max-width:760px; }
.perm-field { margin-bottom:16px; }
.perm-field:last-child { margin-bottom:0; }
.perm-field label { display:block; margin-bottom:8px; font-size:12px; font-weight:700; color:#555; }
.perm-field input, .perm-field textarea { width:100%; border:1px solid #e1dee3; border-radius:10px; padding:11px 12px; font-size:13px; font-family:inherit; }
.perm-field textarea { resize:vertical; }
.perm-btn { display:inline-flex; align-items:center; gap:8px; padding:10px 16px; border-radius:10px; text-decoration:none; border:1px solid #e1dee3; font-size:13px; font-weight:700; background:#fff; color:#222; }
.perm-btn.primary { background:linear-gradient(135deg,#fe5f04,#ff7c30); color:#fff; border:none; }
.perm-form-actions { display:flex; justify-content:flex-end; gap:10px; margin-top:20px; max-width:760px; }
.perm-error { color:#dc2626; font-size:12px; margin-top:6px; }
</style>
@endpush

@section('content')
<div class="perm-editor">
    <div class="perm-editor-top">
        <div>
            <div class="perm-editor-title">Edit Permission</div>
            <div class="perm-editor-sub">Update display labels, modules, and descriptions without changing the permission key.</div>
        </div>
        <a href="{{ route('auth.permissions.index') }}" class="perm-btn">Back</a>
    </div>

    <form method="POST" action="{{ route('auth.permissions.update', $permission) }}">
        @include('pages.auth_menu.permissions.form')
    </form>
</div>
@endsection
