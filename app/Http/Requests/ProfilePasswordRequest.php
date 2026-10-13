<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

/**
 * Form request for self-service password change via the Profile page.
 *
 * @author  RPMS Pro Upgrade
 */
class ProfilePasswordRequest extends FormRequest
{
    /**
     * All authenticated users may change their own password.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string'],
            'new_password'     => [
                'required',
                'confirmed',
                'different:current_password',
                Password::min(10)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'current_password'      => __('current password'),
            'new_password'          => __('new password'),
            'new_password_confirmation' => __('new password confirmation'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'current_password.required'   => __('Please enter your current password to confirm this change.'),
            'new_password.confirmed'       => __('The new password confirmation does not match.'),
            'new_password.different'       => __('The new password must be different from your current password.'),
            'new_password.min'             => __('Password must be at least 10 characters, with uppercase, lowercase, numbers, and symbols.'),
        ];
    }
}
