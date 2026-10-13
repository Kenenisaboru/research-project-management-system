<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Form request for updating a user's own profile (name, email, avatar).
 *
 * @author  RPMS Pro Upgrade
 */
class ProfileUpdateRequest extends FormRequest
{
    /**
     * All authenticated users may update their own profile.
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
        $userId = Auth::id();

        return [
            'name'   => ['sometimes', 'required', 'string', 'max:255'],
            'email'  => ['sometimes', 'required', 'email', 'max:255', 'unique:users,email,' . $userId],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name'   => __('full name'),
            'email'  => __('email address'),
            'avatar' => __('profile photo'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.unique'   => __('This email address is already used by another account.'),
            'avatar.image'   => __('Profile photo must be a valid image file.'),
            'avatar.mimes'   => __('Profile photo must be a JPG, JPEG, PNG, or WebP file.'),
            'avatar.max'     => __('Profile photo may not exceed 2 MB.'),
        ];
    }
}
