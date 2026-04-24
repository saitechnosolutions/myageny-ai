@php
    $editing = isset($role);
@endphp

@csrf
@if($editing)
    @method('PUT')
@endif

<div class="auth-form-grid">
    <div class="auth-form-main">
        <div class="auth-panel">
            <div class="auth-panel-title">Role Details</div>

            <div class="auth-field">
                <label>Role Key</label>
                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $role->name ?? '') }}"
                    placeholder="example: sales_manager"
                    {{ $editing ? 'readonly' : '' }}
                >
                @error('name')<div class="auth-error">{{ $message }}</div>@enderror
            </div>

            <div class="auth-field">
                <label>Display Name</label>
                <input type="text" name="display_name" value="{{ old('display_name', $role->display_name ?? '') }}" placeholder="Sales Manager">
                @error('display_name')<div class="auth-error">{{ $message }}</div>@enderror
            </div>

            <div class="auth-field">
                <label>Description</label>
                <textarea name="description" rows="4" placeholder="Short description for this role">{{ old('description', $role->description ?? '') }}</textarea>
                @error('description')<div class="auth-error">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
</div>

<div class="auth-form-actions">
    <a href="{{ route('auth.roles.index') }}" class="auth-btn">Cancel</a>
    <button type="submit" class="auth-btn auth-btn-primary">{{ $editing ? 'Update Role' : 'Create Role' }}</button>
</div>
