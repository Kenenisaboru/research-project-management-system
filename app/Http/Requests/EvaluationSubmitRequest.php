<?php

namespace App\Http\Requests;

use App\Models\Evaluation;
use App\Services\RbacService;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class EvaluationSubmitRequest
 *
 * Validates peer review evaluations and scoring rubrics submitted under blind review protocols.
 */
class EvaluationSubmitRequest extends FormRequest
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

        $evalId = $this->route('evalId') ?? $this->route('id');
        if ($evalId) {
            $evaluation = Evaluation::where('eval_id', $evalId)->first();
            if ($evaluation && (int) $evaluation->examiner_id !== (int) $user->id && $user->role !== 'admin') {
                return false;
            }
        }

        return $user->role === 'reviewer'
            || RbacService::userHasPermission($user, 'submit_evaluation');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'score'    => ['required', 'numeric', 'min:0', 'max:100'],
            'decision' => ['required', 'in:Accepted,AcceptedWithMinorMods,AcceptedWithMajorMods,Rejected'],
            'comments' => ['required', 'string', 'min:5'],
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
            'score.required'    => __('A peer review score between 0 and 100 is required.'),
            'score.numeric'     => __('The evaluation score must be a numeric value.'),
            'score.min'         => __('The minimum possible evaluation score is 0.'),
            'score.max'         => __('The maximum possible evaluation score is 100.'),
            'decision.required' => __('An explicit evaluation verdict recommendation must be selected.'),
            'decision.in'       => __('Invalid decision outcome selected.'),
            'comments.required' => __('Technical critique and feedback comments are mandatory for peer review audits.'),
            'comments.min'      => __('Feedback comments must be substantive (at least 5 characters).'),
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
            'score'    => __('Evaluation Score'),
            'decision' => __('Verdict Recommendation'),
            'comments' => __('Evaluation Remarks & Critique'),
        ];
    }
}
