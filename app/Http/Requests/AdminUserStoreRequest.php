<?php

namespace App\Http\Requests;

use App\Services\RbacService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * Class AdminUserStoreRequest
 *
 * Validates administrative user creation and account provisioning.
 */
class AdminUserStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        $user = $this->user();
        if (!$user) {
            return false;
        }

        return $user->role === 'admin'
            || RbacService::userHasPermission($user, 'manage_users');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'staff_id' => ['required', 'string', 'max:100', 'unique:users,staff_id'],
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
            'role'     => ['required', 'in:pi,tm,reviewer,dh,coordinator,dean,irerc,vparttcs,rcsc,finance,admin'],
            'dept_id'  => ['nullable', 'integer', 'exists:departments,id'],
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'staff_id.required'  => __('Institutional Staff / Faculty ID is required.'),
            'staff_id.unique'    => __('This Staff ID is already assigned to an existing user.'),
            'name.required'      => __('Full staff or faculty name is required.'),
            'email.required'     => __('Institutional email address is required.'),
            'email.unique'       => __('This email address is already registered in the system.'),
            'password.required'  => __('A secure initial account password is required.'),
            'password.confirmed' => __('Password confirmation does not match.'),
            'role.required'      => __('An operational RBAC role must be assigned to the user.'),
            'role.in'            => __('Selected role is not recognized by the system architecture.'),
            'dept_id.exists'     => __('Assigned academic department is invalid.'),
        ];
    }

    /**
     * Custom attribute names for validation.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'staff_id' => __('Staff ID'),
            'name'     => __('Full Name'),
            'email'    => __('Email Address'),
            'password' => __('Account Password'),
            'role'     => __('System Role'),
            'dept_id'  => __('Department'),
        ];
    }
}
