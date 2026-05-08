<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeOnboardingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $fileRules = ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'];

        return [
            'role_id' => ['nullable', 'exists:roles,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'name' => ['required', 'string', 'max:150'],
            'father_name' => ['nullable', 'string', 'max:150'],
            'correspondence_address' => ['nullable', 'string', 'max:2000'],
            'permanent_address' => ['nullable', 'string', 'max:2000'],
            'mobile' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:150'],
            'date_of_birth' => ['required', 'date'],
            'blood_group' => ['nullable', Rule::in(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])],
            'marital_status' => ['required', Rule::in(['single', 'married'])],
            'date_of_marriage' => ['nullable', 'date', 'required_if:marital_status,married'],
            'aadhaar_card_no' => ['nullable', 'regex:/^\d{12}$/'],
            'pan_card_no' => ['nullable', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/i'],
            'photograph' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],

            'emergency_contact_name' => ['nullable', 'string', 'max:150'],
            'emergency_relation' => ['nullable', 'string', 'max:100'],
            'emergency_contact_no' => ['nullable', 'string', 'max:20'],

            'educations' => ['nullable', 'array'],
            'educations.*.qualification' => ['nullable', 'string', 'max:150'],
            'educations.*.institution_name' => ['nullable', 'string', 'max:255'],
            'educations.*.year_of_passing' => ['nullable', 'string', 'max:20'],
            'educations.*.percentage' => ['nullable', 'string', 'max:20'],
            'educations.*.specialization' => ['nullable', 'string', 'max:255'],

            'employments' => ['nullable', 'array'],
            'employments.*.organisation' => ['nullable', 'string', 'max:255'],
            'employments.*.designation' => ['nullable', 'string', 'max:150'],
            'employments.*.period_from' => ['nullable', 'date'],
            'employments.*.period_to' => ['nullable', 'date'],
            'employments.*.annual_ctc' => ['nullable', 'string', 'max:50'],

            'family_details' => ['nullable', 'array'],
            'family_details.*.name' => ['nullable', 'string', 'max:150'],
            'family_details.*.relation' => ['nullable', 'string', 'max:100'],
            'family_details.*.occupation' => ['nullable', 'string', 'max:150'],
            'family_details.*.date_of_birth' => ['nullable', 'date'],
            'family_details.*.mobile_no' => ['nullable', 'string', 'max:20'],

            'reference_name' => ['nullable', 'string', 'max:150'],
            'reference_organization_name' => ['nullable', 'string', 'max:150'],
            'reference_designation' => ['nullable', 'string', 'max:150'],
            'reference_contact_no' => ['nullable', 'string', 'max:20'],
            'reference_mail_id' => ['nullable', 'email', 'max:150'],

            'bank_name' => ['nullable', 'string', 'max:150'],
            'bank_account_name' => ['nullable', 'string', 'max:150'],
            'bank_account_no' => ['nullable', 'string', 'max:50'],
            'bank_ifsc_code' => ['nullable', 'string', 'max:30'],
            'bank_branch' => ['nullable', 'string', 'max:150'],
            'salary_effective_from' => ['nullable', 'date'],
            'gross_salary' => ['nullable', 'numeric', 'min:0'],
            'basic_salary' => ['nullable', 'numeric', 'min:0'],
            'hra' => ['nullable', 'numeric', 'min:0'],
            'special_allowance' => ['nullable', 'numeric', 'min:0'],
            'other_allowance' => ['nullable', 'numeric', 'min:0'],
            'esi_enabled' => ['nullable', 'boolean'],
            'esi_no' => ['nullable', 'string', 'max:50'],
            'esi_employee_contribution' => ['nullable', 'numeric', 'min:0'],
            'esi_employer_contribution' => ['nullable', 'numeric', 'min:0'],
            'pf_enabled' => ['nullable', 'boolean'],
            'uan_no' => ['nullable', 'string', 'max:50'],
            'pf_account_no' => ['nullable', 'string', 'max:50'],
            'pf_employee_contribution' => ['nullable', 'numeric', 'min:0'],
            'pf_employer_contribution' => ['nullable', 'numeric', 'min:0'],
            'professional_tax' => ['nullable', 'numeric', 'min:0'],
            'tds_amount' => ['nullable', 'numeric', 'min:0'],
            'loan_deduction' => ['nullable', 'numeric', 'min:0'],
            'other_deduction' => ['nullable', 'numeric', 'min:0'],
            'total_deduction' => ['nullable', 'numeric', 'min:0'],
            'net_salary' => ['nullable', 'numeric', 'min:0'],
            'salary_payment_mode' => ['nullable', Rule::in(['bank_transfer', 'cash', 'cheque', 'upi'])],
            'deduction_notes' => ['nullable', 'string', 'max:2000'],

            'declaration_date' => ['nullable', 'date'],
            'declaration_place' => ['nullable', 'string', 'max:150'],
            'signature' => $fileRules,

            'document_10th_marksheet' => $fileRules,
            'document_12th_marksheet' => $fileRules,
            'document_consolidated_marksheet' => $fileRules,
            'document_course_completion_certificate' => $fileRules,
            'document_degree_certificate' => $fileRules,
            'document_provisional_certificate' => $fileRules,
            'document_tc' => $fileRules,
            'document_aadhaar_card' => $fileRules,
            'document_pan_card' => $fileRules,
            'document_voter_id' => $fileRules,
            'document_driving_licence' => $fileRules,
            'document_experience_certificate' => $fileRules,
            'document_salary_slips' => $fileRules,
            'document_bank_passbook' => $fileRules,

            'status' => ['required', Rule::in(['pending', 'verified', 'rejected'])],
        ];
    }
}
