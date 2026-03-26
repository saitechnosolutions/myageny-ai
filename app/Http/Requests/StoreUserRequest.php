<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize()
    {
        // return auth()->user()->can('users.manage');
    }

    public function rules(): array
    {
        return [
            'name'                  => ['required', 'string', 'max:100'],
            'email'                 => ['required', 'email', 'max:150', 'unique:users,email'],
            'phone'                 => ['nullable', 'string', 'max:20'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
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
            'email.unique'       => 'This email is already registered.',
            'password.required'  => 'Password is required.',
            'password.min'       => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'role.required'      => 'Please assign a role to this user.',
            'role.exists'        => 'Selected role does not exist.',
            'avatar.image'       => 'Avatar must be an image file.',
            'avatar.max'         => 'Avatar must not exceed 2MB.',
        ];
    }
}