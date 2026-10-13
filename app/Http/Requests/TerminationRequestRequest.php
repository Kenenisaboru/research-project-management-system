<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Services\RbacService;

/**
 * Form request for requesting a project termination with refund calculation.
 *
 * @author  RPMS Pro Upgrade
 */
class TerminationRequestRequest extends FormRequest
{
    /**
     * Only users with the request_termination permission may initiate termination.
     */
    public function authorize(): bool
    {
        $user = Auth::user();

        return $user && RbacService::userHasPermission($user, 'request_termination');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'reason'          => ['required', 'string', 'min:50', 'max:3000'],
            'verified_value'  => ['required', 'numeric', 'min:0'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'reason'         => __('termination reason'),
            'verified_value' => __('verified expenditure to date (ETB)'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'reason.min'           => __('Please provide at least 50 characters explaining the reason for termination.'),
            'verified_value.min'   => __('Verified expenditure cannot be negative.'),
        ];
    }
}
