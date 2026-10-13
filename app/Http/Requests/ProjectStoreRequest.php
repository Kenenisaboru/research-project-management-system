<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Services\RbacService;

/**
 * Form request for storing (creating + finalising) a new research project proposal.
 *
 * @author  RPMS Pro Upgrade
 */
class ProjectStoreRequest extends FormRequest
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
     * Validation rules applied to a finalised (submit-ready) proposal.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title'             => ['required', 'string', 'max:500'],
            'abstract_text'     => ['required', 'string', 'min:100'],
            'thematic_id'       => ['required', 'integer', 'exists:thematic_areas,id'],
            'requested_budget'  => ['required', 'numeric', 'min:1000'],
            'proposal_document' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:51200'],
        ];
    }

    /**
     * Custom human-readable attribute names used in error messages.
     *
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
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'abstract_text.min'          => __('The :attribute must be at least 100 characters to provide a meaningful summary.'),
            'requested_budget.min'        => __('The :attribute must be at least ETB 1,000.'),
            'proposal_document.required'  => __('A proposal document (PDF or Word) is required before submitting.'),
            'proposal_document.mimes'     => __('The proposal document must be a PDF, DOC, or DOCX file.'),
            'proposal_document.max'       => __('The proposal document may not exceed 50 MB.'),
        ];
    }
}
