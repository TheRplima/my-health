<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PhysicalActivityRequest extends FormRequest
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
            'user_id' => 'nullable|integer|exists:users,id',
            'start_date' => 'nullable|date|required_with:end_date|before_or_equal:end_date',
            'end_date' => 'nullable|date|required_with:start_date|after_or_equal:start_date',
            'category_id' => 'nullable|integer|exists:physical_activity_categories,id',
            'sport_id' => 'nullable|integer|exists:physical_activity_sports,id',
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
            'user_id.integer' => 'The user must be an integer.',
            'user_id.exists' => 'The selected user is invalid.',
            'start_date.date' => 'The start date is not a valid date.',
            'start_date.required_with' => 'The start date field is required when end date is present.',
            'start_date.before_or_equal' => 'The start date must be a date before or equal to end date.',
            'end_date.date' => 'The end date is not a valid date.',
            'end_date.required_with' => 'The end date field is required when start date is present.',
            'end_date.after_or_equal' => 'The end date must be a date after or equal to start date.',
            'category_id.integer' => 'The category must be an integer.',
            'category_id.exists' => 'The selected category is invalid.',
            'sport_id.integer' => 'The sport must be an integer.',
            'sport_id.exists' => 'The selected sport is invalid.',
        ];
    }
}
