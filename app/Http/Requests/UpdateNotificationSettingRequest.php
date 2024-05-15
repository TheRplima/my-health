<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationSettingRequest extends FormRequest
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
            'type' => 'required|string',
            'start' => 'nullable|date_format:H:i',
            'end' => 'nullable|date_format:H:i',
            'interval' => 'nullable|integer',
            'snooze' => 'nullable|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Type is required',
            'type.string' => 'Type must be a string',
            'start.date_format' => 'Start must be a valid time format',
            'end.date_format' => 'End must be a valid time format',
            'interval.integer' => 'Interval must be an integer',
            'snooze.integer' => 'Snooze must be an integer',
        ];
    }
}
