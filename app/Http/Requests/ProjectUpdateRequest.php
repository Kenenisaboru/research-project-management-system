<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;

/**
 * Form request for updating an existing draft / returned / rejected proposal.
 *
 * @author  RPMS Pro Upgrade
 */
class ProjectUpdateRequest extends FormRequest
{
    /**
     * Determine if the authenticated user is authorised to make this request.
     *
     * Only the owning PI may edit, and only whilst the project is in an
     * editable state (Draft, Withdrawn, Returned, Rejected).
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function authorize(): bool
    {
        $user    = Auth::user();
        $project = Project::find($this->route('id'));

        if (! $project || ! $user) {
            return false;
        }

        $editableStatuses = ['Draft', 'Withdrawn', 'Returned', 'Rejected'];

        return $user->role === 'pi'
            && (int) $project->pi_id === (int) $user->id
            && in_array($project->status, $editableStatuses, true);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title'             => ['required', 'string', 'max:500'],
            'abstract_text'     => ['required', 'string', 'min:50'],
            'thematic_id'       => ['required', 'integer', 'exists:thematic_areas,id'],
            'requested_budget'  => ['required', 'numeric', 'min:0'],
            'proposal_document' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:51200'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title'             => __('project title'),
            'abstract_text'     => __('abstract'),
            'thematic_id'       => __('thematic area'),
            'requested_budget'  => __('requested budget'),
            'proposal_document' => __('proposal document'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'abstract_text.min'       => __('The :attribute must be at least 50 characters.'),
            'proposal_document.mimes' => __('The proposal document must be a PDF, DOC, or DOCX file.'),
            'proposal_document.max'   => __('The proposal document may not exceed 50 MB.'),
        ];
    }
}
