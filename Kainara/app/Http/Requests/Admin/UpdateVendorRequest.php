<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVendorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $vendorId = $this->route('vendor')->id;

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('vendors')->ignore($vendorId)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('vendors')->ignore($vendorId)],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'business_type' => ['nullable', 'string', 'max:100'],
            'business_description' => ['nullable', 'string'],
            // 'is_approved' => ['boolean'], // DIHAPUS
        ];
    }
}
