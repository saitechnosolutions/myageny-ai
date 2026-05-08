<?php
// ================================================================
// FILE: app/Http/Requests/Lead/StoreLeadRequest.php
// ================================================================

namespace App\Http\Requests;

use App\Models\Lead;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLeadRequest extends FormRequest
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
            'product_name'  => ['nullable', 'string', 'max:100'],
            'assigned_to'   => ['required', 'exists:users,id'],
            'priority'      => ['required', 'in:low,medium,high'],
            'deal_value'    => ['nullable', 'numeric', 'min:0'],
            'remarks'       => ['nullable', 'string', 'max:2000'],
            'branch_id'     => ['required', 'exists:branches,id'],

        ];
    }

    public function messages(): array
    {
        return [
            'company_name.required'  => 'Company name is required.',
            'contact_name.required'  => 'Contact person name is required.',
            'lead_date.required'     => 'Lead date is required.',
            'mobile_number.required' => 'Mobile number is required.',
            'lead_source.required'   => 'Please select a lead source.',
            'assigned_to.required'   => 'Please select an assigned user.',
            'branch_id.required'     => 'Please select a branch.',
            'lead_status.required'   => 'Please select a lead status.',
            'priority.required'      => 'Please select a priority level.',
        ];
    }
}
