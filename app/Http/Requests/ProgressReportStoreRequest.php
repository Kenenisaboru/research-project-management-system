<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Services\RbacService;

/**
 * Form request for submitting a milestone / progress report.
 *
 * @author  RPMS Pro Upgrade
 */
class ProgressReportStoreRequest extends FormRequest
{
    /**
     * PIs and Team Members with the submit_progress_report permission may submit.
     */
    public function authorize(): bool
    {
        $user = Auth::user();

        return $user && RbacService::userHasPermission($user, 'submit_progress_report');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'milestone_name'           => ['required', 'string', 'max:255'],
            'progress_percentage'      => ['required', 'numeric', 'min:0', 'max:100'],
            'summary_text'             => ['required', 'string', 'min:50'],
            'deliverable_file'         => ['nullable', 'file', 'mimes:pdf,doc,docx,zip,rar,txt,xlsx,csv', 'max:51200'],
            'deliverable_link_url'     => ['nullable', 'url', 'max:500'],
            'deliverable_document_url' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'milestone_name'           => __('milestone name'),
            'progress_percentage'      => __('progress percentage'),
            'summary_text'             => __('progress summary'),
            'deliverable_file'         => __('deliverable file'),
            'deliverable_link_url'     => __('deliverable link'),
            'deliverable_document_url' => __('deliverable document URL'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'progress_percentage.min' => __('Progress cannot be negative.'),
            'progress_percentage.max' => __('Progress cannot exceed 100%.'),
            'summary_text.min'        => __('Please provide at least 50 characters summarising progress.'),
            'deliverable_file.mimes'  => __('Deliverable file must be PDF, Word, ZIP, RAR, TXT, Excel, or CSV.'),
            'deliverable_file.max'    => __('Deliverable file may not exceed 50 MB.'),
            'deliverable_link_url.url'=> __('Please enter a valid URL for the deliverable link.'),
        ];
    }
}
