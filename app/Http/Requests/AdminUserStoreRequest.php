<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

/**
 * Form request for creating a new user account (Admin only).
 *
 * @author  RPMS Pro Upgrade
 */
class AdminUserStoreRequest extends FormRequest
{
    /**
     * Only System Administrators may create user accounts.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'staff_id' => ['required', 'string', 'max:100', 'unique:users,staff_id'],
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => [
                'required',
                'confirmed',
                Password::min(10)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
            'role'    => [
                'required',
                'string',
                'in:pi,tm,reviewer,dh,coordinator,dean,irerc,vparttcs,rcsc,finance,admin',
            ],
            'dept_id' => ['nullable', 'integer', 'exists:departments,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'staff_id' => __('staff ID'),
            'name'     => __('full name'),
            'email'    => __('email address'),
            'password' => __('password'),
            'role'     => __('system role'),
            'dept_id'  => __('department'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'staff_id.unique'  => __('This staff ID is already registered in the system.'),
            'email.unique'     => __('An account with this email address already exists.'),
            'password.min'     => __('Passwords must be at least 10 characters and include uppercase, lowercase, numbers, and symbols.'),
            'role.in'          => __('Please select a valid system role.'),
        ];
    }
}
