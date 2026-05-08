<?php

namespace App\Http\Requests;

class UpdateInternJoiningFormRequest extends StoreInternJoiningFormRequest
{
    public function rules(): array
    {
        return $this->baseRules();
    }
}
