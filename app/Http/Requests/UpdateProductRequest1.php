<?php
// ================================================================
// FILE: app/Http/Requests/Products/UpdateProductRequest.php
// ================================================================

namespace App\Http\Requests\Products;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $productId = $this->route('product')->id;

        return [
            'product_name' => ['required', 'string', 'max:150'],
            'product_code' => ['nullable', 'string', 'max:30', Rule::unique('products','product_code')->ignore($productId)],
            'rate'         => ['required', 'numeric', 'min:0'],
            'gst_percent'  => ['required', 'numeric', 'in:' . implode(',', Product::GST_RATES)],
            'description'  => ['nullable', 'string', 'max:2000'],
            'unit'         => ['required', 'string', 'in:' . implode(',', array_keys(Product::UNITS))],
            'category'     => ['nullable', 'string', 'max:80'],
            'is_active'    => ['boolean'],
        ];
    }
}
