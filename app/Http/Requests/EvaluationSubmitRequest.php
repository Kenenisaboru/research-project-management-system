<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Services\RbacService;

/**
 * Form request for submitting a blind peer-review evaluation score.
 *
 * @author  RPMS Pro Upgrade
 */
class EvaluationSubmitRequest extends FormRequest
{
    /**
     * Only assigned reviewers with the submit_evaluation permission may score.
     */
    public function authorize(): bool
    {
        $user = Auth::user();

        return $user
            && $user->role === 'reviewer'
            && RbacService::userHasPermission($user, 'submit_evaluation');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'score'    => ['required', 'numeric', 'min:0', 'max:100'],
            'decision' => [
                'required',
                'string',
                'in:Accepted,AcceptedWithMinorMods,AcceptedWithMajorMods,Rejected',
            ],
            'comments' => ['required', 'string', 'min:30'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'score'    => __('evaluation score'),
            'decision' => __('peer review decision'),
            'comments' => __('reviewer comments'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'score.min'      => __('The :attribute cannot be negative.'),
            'score.max'      => __('The :attribute cannot exceed 100.'),
            'decision.in'    => __('Please select a valid peer-review decision.'),
            'comments.min'   => __('Please provide at least 30 characters of substantive critique.'),
        ];
    }
}
