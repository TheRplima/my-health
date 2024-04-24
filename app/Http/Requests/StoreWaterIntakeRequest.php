<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWaterIntakeRequest extends FormRequest
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
            'user_id' => 'nullable|exists:users,id',
            'amount' => 'required|numeric'
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.exists' => 'The user id field must exist in the users table.',
            'amount.required' => 'The amount field is required.',
            'amount.numeric' => 'The amount field must be a number.'
        ];
    }
}
