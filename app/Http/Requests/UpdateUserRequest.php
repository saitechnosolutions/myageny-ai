<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        // return auth()->user()->can('users.manage');
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id;

        return [
            'name'                  => ['required', 'string', 'max:100'],
            'email'                 => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($userId)],
            'phone'                 => ['nullable', 'string', 'max:20'],
            'password'              => ['nullable', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['nullable'],
            'branch_id'             => ['nullable', 'exists:branches,id'],
            'role'                  => ['required', 'exists:roles,name'],
            'is_active'             => ['boolean'],
            'avatar'                => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'Full name is required.',
            'email.required'     => 'Email address is required.',
            'email.unique'       => 'This email is already in use.',
            'password.min'       => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'role.required'      => 'Please assign a role to this user.',
        ];
    }
}