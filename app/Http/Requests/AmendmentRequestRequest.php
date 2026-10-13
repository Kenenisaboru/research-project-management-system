<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Services\RbacService;

/**
 * Form request for requesting a budget amendment (supplemental funding).
 *
 * @author  RPMS Pro Upgrade
 */
class AmendmentRequestRequest extends FormRequest
{
    /**
     * Only users with the request_amendment permission may submit budget amendments.
     */
    public function authorize(): bool
    {
        $user = Auth::user();

        return $user && RbacService::userHasPermission($user, 'request_amendment');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'delta_amount'  => ['required', 'numeric', 'min:1000'],
            'justification' => ['required', 'string', 'min:50', 'max:3000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'delta_amount'  => __('additional budget requested (ETB)'),
            'justification' => __('justification'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'delta_amount.min'   => __('The minimum budget amendment is ETB 1,000.'),
            'justification.min'  => __('Justification must be at least 50 characters explaining the need for additional funds.'),
        ];
    }
}
