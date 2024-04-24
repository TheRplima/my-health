<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWaterIntakeContainersRequest extends FormRequest
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
            'name' => 'string',
            'size' => 'integer',
            'icon' => 'string',
            'active' => 'boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required',
            'name.string' => 'Name must be a string',
            'size.required' => 'Size is required',
            'size.integer' => 'Size must be an integer',
            'icon.required' => 'Icon is required',
            'icon.string' => 'Icon must be a string',
            'active.boolean' => 'Active must be a boolean'
        ];
    }
}
