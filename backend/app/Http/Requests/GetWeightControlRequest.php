<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetWeightControlRequest extends FormRequest
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
            'initial_date' => ['nullable', 'date', 'required_with:final_date', 'filled'],
            'final_date' => ['nullable', 'date', 'required_with:initial_date', 'filled', 'after_or_equal:initial_date'],
            'max' => 'nullable|integer'
        ];
    }

    public function messages(): array
    {
        return [
            'initial_date.date' => 'The initial date must be a valid date.',
            'initial_date.required_with' => 'The initial date field is required when final date is present.',
            'initial_date.filled' => 'The initial date field must not be empty.',
            'final_date.date' => 'The final date must be a valid date.',
            'final_date.required_with' => 'The final date field is required when initial date is present.',
            'final_date.filled' => 'The final date field must not be empty.',
            'final_date.after_or_equal' => 'The final date must be a date after or equal to initial date.',
            'max.integer' => 'The max field must be an integer.'
        ];
    }
}
