<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Services\RbacService;

/**
 * Form request for saving a project as a draft (proposal_document is optional).
 *
 * @author  RPMS Pro Upgrade
 */
class ProjectDraftRequest extends FormRequest
{
    /**
     * Determine if the authenticated user is authorised to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::user();

        return $user && $user->role === 'pi'
            && RbacService::userHasPermission($user, 'create_project');
    }

    /**
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
            'proposal_document.mimes' => __('The proposal document must be a PDF, DOC, or DOCX file.'),
            'proposal_document.max'   => __('The proposal document may not exceed 50 MB.'),
        ];
    }
}
