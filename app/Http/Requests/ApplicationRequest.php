<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplicationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'domain' => ['required'],
            'registrar' => ['required'],
            'ns_provider' => ['required'],
            'uses_https' => ['boolean'], //
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
