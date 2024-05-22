<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePhysicalActivitySportRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|integer|exists:physical_activity_categories,id',
            'calories_burned_per_minute' => 'required|integer',
            'metabolic_equivalent' => 'required|numeric',
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'description.string' => 'The description must be a string.',
            'category_id.required' => 'The category field is required.',
            'category_id.integer' => 'The category must be an integer.',
            'category_id.exists' => 'The selected category is invalid.',
            'category_id.exists' => 'The selected category is invalid.',
            'calories_burned_per_minute.required' => 'The calories burned per minute field is required.',
            'calories_burned_per_minute.integer' => 'The calories burned per minute must be an integer.',
            'metabolic_equivalent.required' => 'The metabolic equivalent field is required.',
            'metabolic_equivalent.numeric' => 'The metabolic equivalent must be a number.',
        ];
    }
}
