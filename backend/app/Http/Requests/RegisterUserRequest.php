<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
            'password_confirmation' => ['required', 'string', 'same:password'],
            'phone'     => ['nullable', 'numeric', 'digits_between:10,11'],
            'gender' => ['nullable', 'string', 'max:1'],
            'dob' => ['nullable', 'date', 'before:-14 years'],
            'height' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'daily_water_amount' => 'nullable|numeric',
            'activity_level' => 'nullable|numeric',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required',
            'name.string' => 'Name must be a string',
            'name.max' => 'Name must not be greater than 255 characters',
            'email.required' => 'Email is required',
            'email.string' => 'Email must be a string',
            'email.email' => 'Email is invalid',
            'email.max' => 'Email must not be greater than 255 characters',
            'email.unique' => 'Email is already taken',
            'password.required' => 'Password is required',
            'password.string' => 'Password must be a string',
            'password.min' => 'Password must be at least 6 characters',
            'password_confirmation.required' => 'Password confirmation is required',
            'password_confirmation.string' => 'Password confirmation must be a string',
            'password_confirmation.same' => 'Password confirmation must be the same as password',
            'phone.numeric' => 'Phone must be a number',
            'phone.digits_between' => 'Phone must be between 10 and 11 digits',
            'gender.string' => 'Gender  must be a string',
            'gender.max' => 'Gender must not be greater than 1 character',
            'dob.date' => 'Date of birth must be a date',
            'dob.before' => 'You must be at least 14 years old',
            'height.numeric' => 'Height must be a number',
            'weight.numeric' => 'Weight must be a number',
            'daily_water_amount.numeric' => 'Daily water amount must be a number',
            'activity_level.numeric' => 'Activity level must be a number',
        ];
    }
}
