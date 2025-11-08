<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:customers,email,' . $this->route('id'),
            'phone' => 'sometimes|required|string|max:255',
            'birthdate' => 'sometimes|required|date',
            'address' => 'sometimes|required|string|max:255',
            'address_line_two' => 'sometimes|required|string|max:255',
            'neighborhood' => 'sometimes|required|string|max:255',
            'zip_code' => 'sometimes|required|string|max:255',
        ];
    }
}
