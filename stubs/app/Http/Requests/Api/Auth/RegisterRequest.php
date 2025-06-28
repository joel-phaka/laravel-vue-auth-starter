<?php


namespace App\Http\Requests\Api\Auth;


use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:48',
            'last_name' => 'required|string|max:48',
            'email' => 'required|string|email|max:128|unique:users',
            'password' => 'required|min:8|max:60|confirmed',
            'password_confirmation' => 'required|same:password',
            'accept_terms' => 'accepted'
        ];
    }

    public function messages(): array
    {
        return [
            'accept_terms.accepted' => 'The Terms and Conditions must be accepted.'
        ];
    }
}
