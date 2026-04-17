<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends StoreProductRequest
{
    // Inherits all rules from StoreProductRequest.
    // Override only what differs for update.
    public function rules(): array
    {
        return parent::rules();
    }
}
