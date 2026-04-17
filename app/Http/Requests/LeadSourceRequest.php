<?php
// app/Http/Requests/LeadSourceRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LeadSourceRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
        ];
    }
}
