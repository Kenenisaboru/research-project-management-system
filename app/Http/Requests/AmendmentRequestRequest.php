<?php

namespace App\Http\Requests;

use App\Models\Project;
use App\Services\RbacService;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class AmendmentRequestRequest
 *
 * Validates budget amendment and supplemental fund requests submitted by Principal Investigators.
 */
class AmendmentRequestRequest extends FormRequest
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

        $projectId = $this->route('projectId') ?? $this->route('id') ?? $this->route('project');
        if ($projectId) {
            $project = Project::find($projectId);
            if ($project && (int) $project->pi_id !== (int) $user->id && $user->role !== 'admin') {
                return false;
            }
        }

        return $user->role === 'pi' || $user->role === 'admin'
            || RbacService::userHasPermission($user, 'request_amendment');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'delta_amount'  => ['required', 'numeric', 'min:0'],
            'justification' => ['required', 'string', 'min:10'],
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
            'delta_amount.required'  => __('Requested additional amendment budget amount (ETB) is required.'),
            'delta_amount.numeric'   => __('Amendment amount must be numeric.'),
            'delta_amount.min'       => __('Amendment amount cannot be negative.'),
            'justification.required' => __('A financial and technical justification for the budget amendment is required.'),
            'justification.min'      => __('Justification must be at least 10 characters.'),
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
            'delta_amount'  => __('Requested Supplemental Budget'),
            'justification' => __('Amendment Justification'),
        ];
    }
}
