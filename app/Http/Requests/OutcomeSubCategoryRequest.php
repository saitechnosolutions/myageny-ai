<?php
// app/Http/Requests/OutcomeSubCategoryRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OutcomeSubCategoryRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'category_id' => 'required|exists:outcome_categories,id',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Please select an Outcome Category.',
            'category_id.exists'   => 'The selected category does not exist.',
        ];
    }
}
