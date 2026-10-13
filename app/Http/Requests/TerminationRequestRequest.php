<?php

namespace App\Http\Requests;

use App\Models\Project;
use App\Services\RbacService;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class TerminationRequestRequest
 *
 * Validates premature project termination requests and financial refund calculations.
 */
class TerminationRequestRequest extends FormRequest
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
            || RbacService::userHasPermission($user, 'request_termination');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'reason'         => ['required', 'string', 'min:10'],
            'verified_value' => ['required', 'numeric', 'min:0'],
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
            'reason.required'         => __('Reason for project termination is required.'),
            'reason.min'              => __('Termination justification must be at least 10 characters.'),
            'verified_value.required' => __('Verified delivered value (ETB) must be provided for refund calculation.'),
            'verified_value.numeric'  => __('Verified value must be a valid numeric amount.'),
            'verified_value.min'      => __('Verified delivered value cannot be negative.'),
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
            'reason'         => __('Termination Reason'),
            'verified_value' => __('Verified Delivered Value'),
        ];
    }
}
