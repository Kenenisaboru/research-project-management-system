<?php

namespace App\Http\Requests;

use App\Models\Project;
use App\Models\ProjectMember;
use App\Services\RbacService;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ProgressReportStoreRequest
 *
 * Validates milestone progress reports and deliverable uploads submitted by PIs and team members.
 */
class ProgressReportStoreRequest extends FormRequest
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

        $projectId = $this->route('projectId') ?? $this->route('project') ?? $this->input('project_id');
        if ($projectId) {
            $project = Project::where('project_id', $projectId)->first();
            if ($project) {
                if ($user->role === 'pi' && (int) $project->pi_id !== (int) $user->id && $user->role !== 'admin') {
                    return false;
                }
                if ($user->role === 'tm') {
                    $isMember = ProjectMember::where('project_id', $projectId)->where('user_id', $user->id)->exists();
                    if (!$isMember) {
                        return false;
                    }
                }
            }
        }

        return in_array($user->role, ['pi', 'tm', 'admin'])
            || RbacService::userHasPermission($user, 'submit_progress_report');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $projectId = $this->route('projectId') ?? $this->route('project') ?? $this->input('project_id');
        $minProgress = 0;

        if ($projectId) {
            $project = Project::where('project_id', $projectId)->first();
            if ($project) {
                $minProgress = (float) ($project->milestoneReports()->max('progress_percentage') ?? 0);
            }
        }

        return [
            'milestone_name'           => ['required', 'string', 'max:255'],
            'progress_percentage'      => ['required', 'numeric', 'min:' . $minProgress, 'max:100'],
            'summary_text'             => ['required', 'string'],
            'deliverable_file'         => ['nullable', 'file', 'mimes:pdf,doc,docx,zip,rar,txt,xlsx,csv', 'max:51200'],
            'deliverable_link_url'     => ['nullable', 'url', 'max:500'],
            'deliverable_document_url' => ['nullable', 'string', 'max:500'],
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
            'milestone_name.required'      => __('A milestone or reporting phase title is required.'),
            'progress_percentage.required' => __('Cumulative completion percentage is required.'),
            'progress_percentage.min'      => __('Progress percentage cannot decrease below previously approved milestone completion level.'),
            'progress_percentage.max'      => __('Cumulative progress percentage cannot exceed 100%.'),
            'summary_text.required'        => __('A detailed summary of activities and achievements is required.'),
            'deliverable_file.mimes'       => __('Deliverable file must be a document, spreadsheet, text, or archive file.'),
            'deliverable_file.max'         => __('Deliverable attachment size cannot exceed 50MB.'),
            'deliverable_link_url.url'     => __('Deliverable reference link must be a valid URL.'),
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
            'milestone_name'       => __('Milestone Name'),
            'progress_percentage'  => __('Progress Percentage'),
            'summary_text'         => __('Milestone Summary'),
            'deliverable_file'     => __('Deliverable Attachment'),
            'deliverable_link_url' => __('Deliverable Web Link'),
        ];
    }
}
