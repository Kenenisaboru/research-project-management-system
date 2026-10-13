<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Form request for disbursing a budget request (Finance Office only).
 *
 * @author  RPMS Pro Upgrade
 */
class BudgetDisbursementRequest extends FormRequest
{
    /**
     * Only Finance Office users with the process_disbursement permission may disburse.
     */
    public function authorize(): bool
    {
        $user = Auth::user();

        return $user && $user->role === 'finance';
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'payment_method' => ['required', 'string', 'in:Cash,Check,Bank Transfer'],
            'notes'          => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'payment_method' => __('payment method'),
            'notes'          => __('payment notes / reference'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'payment_method.in' => __('Payment method must be Cash, Check, or Bank Transfer.'),
        ];
    }
}
