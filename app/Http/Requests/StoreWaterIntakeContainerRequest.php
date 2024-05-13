<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWaterIntakeContainerRequest extends FormRequest
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
            'user_id' => 'required|integer|exists:users,id',
            'name' => 'required|string',
            'size' => 'required|integer',
            'icon' => 'nullable|string',
            'active' => 'boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'User ID is required',
            'user_id.integer' => 'User ID must be an integer',
            'user_id.exists' => 'User ID must exist in the users table',
            'name.required' => 'Name is required',
            'name.string' => 'Name must be a string',
            'size.required' => 'Size is required',
            'size.integer' => 'Size must be an integer',
            'icon.string' => 'Icon must be a string',
            'active.boolean' => 'Active must be a boolean'
        ];
    }
}
