<?php

namespace App\Http\Requests;

use App\Models\Project;
use App\Services\RbacService;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ProjectUpdateRequest
 *
 * Validates updates made to editable research proposals by Principal Investigators.
 */
class ProjectUpdateRequest extends FormRequest
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

        $projectId = $this->route('id') ?? $this->route('project');
        if ($projectId) {
            $project = Project::find($projectId);
            if ($project && (int) $project->pi_id !== (int) $user->id && $user->role !== 'admin') {
                return false;
            }
        }

        return $user->role === 'pi' || RbacService::userHasPermission($user, 'create_project');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title'             => ['required', 'string', 'max:500'],
            'abstract_text'     => ['required', 'string'],
            'thematic_id'       => ['required', 'integer', 'exists:thematic_areas,id'],
            'requested_budget'  => ['required', 'numeric', 'min:0'],
            'proposal_document' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:51200'],
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
            'title.required'             => __('A descriptive project title is required.'),
            'title.max'                  => __('The project title must not exceed 500 characters.'),
            'abstract_text.required'     => __('The research proposal abstract cannot be empty.'),
            'thematic_id.required'       => __('Please select a valid institutional thematic research area.'),
            'thematic_id.exists'         => __('The selected thematic area is invalid or inactive.'),
            'requested_budget.required'  => __('Please specify the requested project budget in ETB.'),
            'requested_budget.min'       => __('Requested budget cannot be negative.'),
            'proposal_document.mimes'    => __('Proposal document must be a PDF, DOC, or DOCX file.'),
            'proposal_document.max'      => __('Proposal document file size must not exceed 50MB.'),
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
            'title'             => __('Project Title'),
            'abstract_text'     => __('Abstract / Summary'),
            'thematic_id'       => __('Thematic Area'),
            'requested_budget'  => __('Requested Budget'),
            'proposal_document' => __('Proposal Document'),
        ];
    }
}
