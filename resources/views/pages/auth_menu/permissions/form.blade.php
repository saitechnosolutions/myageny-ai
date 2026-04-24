@php
    $editing = isset($permission);
@endphp

@csrf
@if($editing)
    @method('PUT')
@endif

<div class="perm-form-card">
    @unless($editing)
        <div class="perm-field">
            <label>Module Name</label>
            <input type="text" name="module" value="{{ old('module') }}" placeholder="example: users">
            @error('module')<div class="perm-error">{{ $message }}</div>@enderror
        </div>

        <div class="perm-field">
            <label>Default Actions</label>
            <div class="perm-actions-grid">
                @foreach($defaultActions as $action)
                    <label class="perm-check-card">
                        <input type="checkbox" name="actions[]" value="{{ $action }}" {{ in_array($action, old('actions', $defaultActions), true) ? 'checked' : '' }}>
                        <span>{{ ucfirst($action) }}</span>
                    </label>
                @endforeach
            </div>
            @error('actions')<div class="perm-error">{{ $message }}</div>@enderror
            @error('actions.*')<div class="perm-error">{{ $message }}</div>@enderror
        </div>

        <div class="perm-field">
            <label>Custom Actions</label>
            <input type="text" name="custom_actions" value="{{ old('custom_actions') }}" placeholder="example: export, assign, download">
            <div class="perm-help">Add extra actions separated by commas or spaces.</div>
            @error('custom_actions')<div class="perm-error">{{ $message }}</div>@enderror
        </div>
    @else
        <div class="perm-field">
            <label>Permission Key</label>
            <input type="text" value="{{ $permission->name }}" readonly>
        </div>
    @endunless

    @if($editing)
        <div class="perm-field">
            <label>Display Name</label>
            <input type="text" name="display_name" value="{{ old('display_name', $permission->display_name ?? '') }}" placeholder="Manage Users">
            @error('display_name')<div class="perm-error">{{ $message }}</div>@enderror
        </div>
    @endif

    @if($editing)
        <div class="perm-field">
            <label>Module</label>
            <input type="text" name="module" value="{{ old('module', $permission->module ?? '') }}" placeholder="users">
            @error('module')<div class="perm-error">{{ $message }}</div>@enderror
        </div>
    @endif

    <div class="perm-field">
        <label>{{ $editing ? 'Description' : 'Shared Description' }}</label>
        <textarea name="description" rows="4" placeholder="{{ $editing ? 'Short explanation of what this permission allows' : 'Optional description applied to all newly created permissions' }}">{{ old('description', $permission->description ?? '') }}</textarea>
        @error('description')<div class="perm-error">{{ $message }}</div>@enderror
    </div>
</div>

<div class="perm-form-actions">
    <a href="{{ route('auth.permissions.index') }}" class="perm-btn">Cancel</a>
    <button type="submit" class="perm-btn primary">{{ $editing ? 'Update Permission' : 'Create Permissions' }}</button>
</div>
