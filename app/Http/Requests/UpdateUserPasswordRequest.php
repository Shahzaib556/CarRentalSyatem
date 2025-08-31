<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUserPasswordRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'current_user_password' => ['required', 'current_password:sanctum'],
            'new_user_password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
            ],
        ];
    }

    public function messages()
    {
        return [
            'current_user_password.current_password' => 'The current user password is incorrect',
            'new_user_password.uncompromised' => 'This password has appeared in a data breach. Please choose a different password.'
        ];
    }
}