<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Services\RbacService;

/**
 * Form request for adding a team member to a project.
 *
 * @author  RPMS Pro Upgrade
 */
class TeamMemberStoreRequest extends FormRequest
{
    /**
     * Only PIs with the manage_team permission may add team members.
     */
    public function authorize(): bool
    {
        $user = Auth::user();

        return $user && RbacService::userHasPermission($user, 'manage_team');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'user_id'                 => ['required', 'integer', 'exists:users,id'],
            'role_in_project'         => ['required', 'string', 'max:255'],
            'contribution_percentage' => ['required', 'numeric', 'min:1', 'max:100'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'user_id'                 => __('team member'),
            'role_in_project'         => __('role in project'),
            'contribution_percentage' => __('contribution percentage'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'user_id.exists'                 => __('The selected team member does not exist in the system.'),
            'contribution_percentage.min'    => __('Contribution percentage must be at least 1%.'),
            'contribution_percentage.max'    => __('Contribution percentage cannot exceed 100%.'),
        ];
    }
}
