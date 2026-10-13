<?php

namespace App\Http\Requests;

use App\Services\RbacService;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class AdminUserUpdateRequest
 *
 * Validates administrative updates to user accounts, departmental associations, and statuses.
 */
class AdminUserUpdateRequest extends FormRequest
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
        $userId = $this->route('id') ?? $this->route('user');

        return [
            'staff_id' => ['required', 'string', 'max:100', 'unique:users,staff_id,' . $userId],
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email,' . $userId],
            'role'     => ['required', 'in:pi,tm,reviewer,dh,coordinator,dean,irerc,vparttcs,rcsc,finance,admin'],
            'dept_id'  => ['nullable', 'integer', 'exists:departments,id'],
            'status'   => ['required', 'in:active,inactive'],
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
            'staff_id.required' => __('Institutional Staff / Faculty ID is required.'),
            'staff_id.unique'   => __('This Staff ID is already assigned to another user.'),
            'name.required'     => __('Full user name is required.'),
            'email.required'    => __('User email address is required.'),
            'email.unique'      => __('This email address belongs to another registered user.'),
            'role.required'     => __('An operational system role is required.'),
            'status.required'   => __('User account status (active/inactive) must be specified.'),
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
            'role'     => __('System Role'),
            'dept_id'  => __('Department'),
            'status'   => __('Account Status'),
        ];
    }
}
