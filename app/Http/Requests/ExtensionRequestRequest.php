<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Services\RbacService;

/**
 * Form request for requesting a project time extension.
 *
 * @author  RPMS Pro Upgrade
 */
class ExtensionRequestRequest extends FormRequest
{
    /**
     * Only PIs with the request_extension permission may request extensions.
     */
    public function authorize(): bool
    {
        $user = Auth::user();

        return $user && RbacService::userHasPermission($user, 'request_extension');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'min:30', 'max:2000'],
            'months' => ['required', 'integer', 'min:1', 'max:6'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'reason' => __('extension reason'),
            'months' => __('number of months'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'reason.min'  => __('Please provide a detailed justification (at least 30 characters) for the extension.'),
            'months.min'  => __('Extension must be at least 1 month.'),
            'months.max'  => __('A single extension request may not exceed 6 months.'),
        ];
    }
}
