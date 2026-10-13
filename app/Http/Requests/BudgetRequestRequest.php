<?php

namespace App\Http\Requests;

use App\Services\RbacService;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class BudgetRequestRequest
 *
 * Validates project budget approvals and governance tier decisions (Dean / RCSC / VP).
 */
class BudgetRequestRequest extends FormRequest
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

        return in_array($user->role, ['dean', 'rcsc', 'vparttcs', 'admin', 'finance'])
            || RbacService::userHasPermission($user, 'approve_budget');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'approved_budget' => ['sometimes', 'required', 'numeric', 'min:0'],
            'decision'        => ['sometimes', 'required', 'in:Approved,Rejected'],
            'approved_amount' => ['nullable', 'numeric', 'min:0'],
            'comments'        => ['nullable', 'string', 'max:2000'],
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
            'approved_budget.required' => __('Please specify the approved budget allocation in ETB.'),
            'approved_budget.min'      => __('Approved budget must be at least 0 ETB.'),
            'decision.required'        => __('A governance approval decision (Approved or Rejected) is required.'),
            'decision.in'              => __('Decision must be either Approved or Rejected.'),
            'approved_amount.numeric'  => __('Approved amount must be a valid numeric figure.'),
            'approved_amount.min'      => __('Approved amount cannot be negative.'),
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
            'approved_budget' => __('Approved Budget'),
            'decision'        => __('Governance Decision'),
            'approved_amount' => __('Approved Amount'),
            'comments'        => __('Review Feedback / Comments'),
        ];
    }
}
