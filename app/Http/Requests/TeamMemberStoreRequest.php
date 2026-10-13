<?php

namespace App\Http\Requests;

use App\Models\Project;
use App\Services\RbacService;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class TeamMemberStoreRequest
 *
 * Validates adding or updating co-investigators and research assistants within a project team.
 */
class TeamMemberStoreRequest extends FormRequest
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

        $projectId = $this->route('projectId') ?? $this->route('project');
        if ($projectId) {
            $project = Project::where('project_id', $projectId)->first();
            if ($project && (int) $project->pi_id !== (int) $user->id && $user->role !== 'admin') {
                return false;
            }
        }

        return $user->role === 'pi' || $user->role === 'admin'
            || RbacService::userHasPermission($user, 'manage_team');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [
            'role_in_project'         => ['required', 'string', 'max:255'],
            'contribution_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
        ];

        if ($this->isMethod('post')) {
            $rules['user_id'] = ['required', 'integer', 'exists:users,id'];
        }

        return $rules;
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'user_id.required'                 => __('Please select a valid researcher account to add to the team.'),
            'user_id.exists'                   => __('The designated user account does not exist.'),
            'role_in_project.required'         => __('Specify the investigator designation or functional role in the project.'),
            'contribution_percentage.required' => __('Please specify the expected research effort/contribution percentage.'),
            'contribution_percentage.numeric'  => __('Contribution percentage must be a number.'),
            'contribution_percentage.min'      => __('Contribution percentage cannot be negative.'),
            'contribution_percentage.max'      => __('Contribution percentage cannot exceed 100%.'),
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
            'user_id'                 => __('Team Member'),
            'role_in_project'         => __('Role Designation'),
            'contribution_percentage' => __('Contribution Effort (%)'),
        ];
    }
}
