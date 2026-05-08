<?php

namespace App\Http\Requests;

use App\Models\Lead;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLeadRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'company_name'  => ['required', 'string', 'max:150'],
            'contact_name'  => ['required', 'string', 'max:100'],
            'lead_date'     => ['required', 'date'],
            'mobile_number' => ['required', 'string', 'max:20'],
            'email'         => ['nullable', 'email', 'max:150'],
            'lead_source'   => ['required', 'string', Rule::in(Lead::sourceKeys())],
            'lead_status'   => ['required', 'string', Rule::in(Lead::statusKeys())],
            'product_name'  => ['nullable', 'string', 'max:100'],
            'assigned_to'   => ['required', 'exists:users,id'],
            'priority'      => ['required', 'in:low,medium,high'],
            'deal_value'    => ['nullable', 'numeric', 'min:0'],
            'remarks'       => ['nullable', 'string', 'max:2000'],
            'branch_id'     => ['required', 'exists:branches,id'],
        ];
    }
}
