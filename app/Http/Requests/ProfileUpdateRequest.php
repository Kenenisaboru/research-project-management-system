<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ProfileUpdateRequest
 *
 * Validates self-service personal profile updates and profile avatar uploads.
 */
class ProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $userId = $this->user()?->id;

        return [
            'name'   => ['sometimes', 'required', 'string', 'max:255'],
            'email'  => ['sometimes', 'required', 'email', 'max:255', 'unique:users,email,' . $userId],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
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
            'name.required'  => __('Full name is required.'),
            'email.required' => __('Valid email address is required.'),
            'email.unique'   => __('This email address is already registered to another user.'),
            'avatar.image'   => __('Profile avatar must be an image file.'),
            'avatar.mimes'   => __('Profile avatar format must be JPG, JPEG, PNG, or WEBP.'),
            'avatar.max'     => __('Profile photo size must not exceed 2MB.'),
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
            'name'   => __('Full Name'),
            'email'  => __('Email Address'),
            'avatar' => __('Profile Avatar Photo'),
        ];
    }
}
