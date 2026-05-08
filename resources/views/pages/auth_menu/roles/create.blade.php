@extends('layouts.app')

@section('title', 'Create Role')

@push('styles')
<style>
.auth-editor { padding: 28px; background: #f4f5f7; min-height: 100%; }
.auth-editor-top { display:flex; justify-content:space-between; align-items:center; gap:16px; margin-bottom:20px; }
.auth-editor-title { font-size:22px; font-weight:800; color:#121212; }
.auth-editor-sub { font-size:13px; color:#7c7c7c; margin-top:4px; }
.auth-form-grid { display:grid; grid-template-columns: minmax(0, 1.1fr) minmax(320px, .9fr); gap:20px; }
.auth-panel { background:#fff; border:1px solid #e1dee3; border-radius:16px; padding:20px; }
.auth-panel-title { font-size:15px; font-weight:800; color:#121212; margin-bottom:16px; }
.auth-field { margin-bottom:16px; }
.auth-field:last-child { margin-bottom:0; }
.auth-field label { display:block; font-size:12px; font-weight:700; color:#555; margin-bottom:8px; }
.auth-field input, .auth-field textarea, .auth-field select { width:100%; border:1px solid #e1dee3; border-radius:10px; padding:11px 12px; font-size:13px; font-family:inherit; background:#fff; }
.auth-field textarea { resize:vertical; }
.auth-perm-groups { display:flex; flex-direction:column; gap:14px; max-height:560px; overflow:auto; }
.auth-perm-group { border:1px solid #f0eef2; border-radius:12px; padding:14px; }
.auth-perm-head { font-size:11px; font-weight:800; letter-spacing:.8px; color:#8a8a8a; margin-bottom:12px; }
.auth-perm-list { display:flex; flex-direction:column; gap:10px; }
.auth-check { display:flex; gap:10px; align-items:flex-start; cursor:pointer; }
.auth-check input { margin-top:3px; }
.auth-check strong { display:block; font-size:13px; color:#121212; }
.auth-check small { display:block; color:#8a8a8a; font-size:11px; margin-top:2px; }
.auth-btn { display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:10px 16px; border-radius:10px; text-decoration:none; border:1px solid #e1dee3; font-size:13px; font-weight:700; background:#fff; color:#222; }
.auth-btn-primary { background:linear-gradient(135deg,#fe5f04,#ff7c30); color:#fff; border:none; }
.auth-form-actions { display:flex; justify-content:flex-end; gap:10px; margin-top:20px; }
.auth-error { color:#dc2626; font-size:12px; margin-top:6px; }
.auth-muted { color:#8a8a8a; font-size:13px; }
</style>
@endpush

@section('content')
<div class="auth-editor">
    <div class="auth-editor-top">
        <div>
            <div class="auth-editor-title">Create Role</div>
            <div class="auth-editor-sub">Add a new role and assign the permissions it should inherit.</div>
        </div>
        <a href="{{ route('auth.roles.index') }}" class="auth-btn">Back</a>
    </div>

    <form method="POST" action="{{ route('auth.roles.store') }}">
        @include('pages.auth_menu.roles.form')
    </form>
</div>
@endsection
