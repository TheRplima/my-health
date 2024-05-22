<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePhysicalActivityRequest extends FormRequest
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
            'sport_id' => 'required|integer|exists:physical_activity_sports,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'effort_level' => 'nullable|string|in:low,medium,high',
            'observations' => 'nullable|string',
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
            'sport_id.required' => 'The sport field is required.',
            'sport_id.integer' => 'The sport must be an integer.',
            'sport_id.exists' => 'The selected sport is invalid.',
            'date.required' => 'The date field is required.',
            'date.date' => 'The date is not a valid date.',
            'start_time.required' => 'The start time field is required.',
            'start_time.date_format' => 'The start time is not a valid time.',
            'end_time.required' => 'The end time field is required.',
            'end_time.date_format' => 'The end time is not a valid time.',
            'effort_level.integer' => 'The effort level must be an integer.',
            'observations.string' => 'The observations must be a string.',
        ];
    }
}
