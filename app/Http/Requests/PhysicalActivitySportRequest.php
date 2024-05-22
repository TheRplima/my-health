<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PhysicalActivitySportRequest extends FormRequest
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
        //get all physical activities from user, allowing filter by date range, category and sport
        return [
            'category_id' => 'nullable|integer|exists:physical_activity_categories,id'
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
            'category_id.integer' => 'The category must be an integer.',
            'category_id.exists' => 'The selected category is invalid.',
        ];
    }
}
