<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FacilityManagementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:191'],
            'office_mopping_date' => ['nullable', 'date'],
            'office_cleaning_date' => ['nullable', 'date'],
            'toilet_cleaning_date' => ['nullable', 'date'],
            'remarks' => ['nullable', 'string'],
        ];
    }
}
