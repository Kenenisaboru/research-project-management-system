<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Form request for updating an existing user account (Admin only).
 *
 * @author  RPMS Pro Upgrade
 */
class AdminUserUpdateRequest extends FormRequest
{
    /**
     * Only System Administrators may update user accounts.
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
        // The route parameter name is 'id' (see web.php: /admin/users/{id})
        $userId = $this->route('id');

        return [
            'staff_id' => ['required', 'string', 'max:100', 'unique:users,staff_id,' . $userId],
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email,' . $userId],
            'role'     => [
                'required',
                'string',
                'in:pi,tm,reviewer,dh,coordinator,dean,irerc,vparttcs,rcsc,finance,admin',
            ],
            'dept_id'  => ['nullable', 'integer', 'exists:departments,id'],
            'status'   => ['required', 'string', 'in:active,inactive'],
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
            'role'     => __('system role'),
            'dept_id'  => __('department'),
            'status'   => __('account status'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'staff_id.unique' => __('This staff ID is already assigned to another user.'),
            'email.unique'    => __('Another account is already using this email address.'),
            'role.in'         => __('Please select a valid system role.'),
            'status.in'       => __('Account status must be either active or inactive.'),
        ];
    }
}
