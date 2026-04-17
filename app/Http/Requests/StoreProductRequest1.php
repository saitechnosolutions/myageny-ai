<?php
// ================================================================
// FILE: app/Http/Requests/Products/StoreProductRequest.php
// ================================================================

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'product_name' => ['required', 'string', 'max:150'],
            'product_code' => ['nullable', 'string', 'max:30', 'unique:products,product_code'],
            'rate'         => ['required', 'numeric', 'min:0'],
            'gst_percent'  => ['required', 'numeric', 'in:' . implode(',', Product::GST_RATES)],
            'description'  => ['nullable', 'string', 'max:2000'],
            'unit'         => ['required', 'string', 'in:' . implode(',', array_keys(Product::UNITS))],
            'category'     => ['nullable', 'string', 'max:80'],
            'is_active'    => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_name.required' => 'Product name is required.',
            'rate.required'         => 'Rate (selling price) is required.',
            'rate.min'              => 'Rate must be zero or greater.',
            'gst_percent.required'  => 'Please select a GST rate.',
            'gst_percent.in'        => 'Invalid GST rate selected.',
            'unit.required'         => 'Please select a unit.',
        ];
    }
}
