<?php

namespace App\Http\Requests;

use App\Models\Project;
use App\Services\RbacService;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ExtensionRequestRequest
 *
 * Validates project timeline extension requests submitted by Principal Investigators.
 */
class ExtensionRequestRequest extends FormRequest
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
            || RbacService::userHasPermission($user, 'request_extension');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'min:10'],
            'months' => ['required', 'integer', 'min:1', 'max:6'],
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
            'reason.required' => __('A substantive scientific justification for the requested extension is required.'),
            'reason.min'      => __('Justification must be at least 10 characters.'),
            'months.required' => __('Number of extension months must be specified.'),
            'months.integer'  => __('Extension duration must be an integer number of months.'),
            'months.min'      => __('Minimum extension duration is 1 month.'),
            'months.max'      => __('Institutional policy caps single extension requests at 6 months.'),
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
            'reason' => __('Extension Justification'),
            'months' => __('Requested Extension Months'),
        ];
    }
}
