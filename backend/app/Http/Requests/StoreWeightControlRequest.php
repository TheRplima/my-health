<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWeightControlRequest extends FormRequest
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
            'user_id' => 'nullable|integer|exists:users,id',
            'date' => 'nullable|date',
            'weight' => 'required|numeric',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'The user id field is required.',
            'user_id.integer' => 'The user id field must be an integer.',
            'user_id.exists' => 'The user id field must exist in the users table.',
            'date.date' => 'The date field must be a valid date.',
            'weight.required' => 'The weight field is required.',
            'weight.numeric' => 'The weight field must be a number.',
        ];
    }
}
