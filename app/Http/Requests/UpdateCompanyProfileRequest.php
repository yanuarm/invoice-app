<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name' => ['nullable', 'string', 'max:255'],
            'company_email' => ['nullable', 'email', 'max:255'],
            'company_phone' => ['nullable', 'string', 'max:50'],
            'company_address' => ['nullable', 'string'],
            'company_logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'invoice_prefix' => ['nullable', 'string', 'max:10'],
            'invoice_footer' => ['nullable', 'string'],
        ];
    }
}
