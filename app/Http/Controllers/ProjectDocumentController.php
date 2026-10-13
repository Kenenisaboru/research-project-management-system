<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectDocument;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProjectDocumentController extends Controller
{
    /**
     * Store an uploaded project document with automatic versioning.
     */
    public function store(Request $request, int $projectId): RedirectResponse
    {
        $request->validate([
            'title'         => 'required|string|max:255',
            'document_type' => 'required|string|in:Proposal,Ethics_Clearance,Contract,Milestone_Report,Final_Report,Financial_Receipt,Supporting_Document',
            'file'          => 'required|file|max:10240|mimes:pdf,doc,docx,xlsx,xls,zip,png,jpg,jpeg',
            'notes'         => 'nullable|string|max:1000',
        ]);

        $project = Project::where('project_id', $projectId)->firstOrFail();
        $user = Auth::user();

        // Authorization: PI, Team Member, Coordinator, or Admin
        $isPi = (int) $project->pi_id === (int) $user->id;
        $isTm = $project->teamMembers()->where('user_id', $user->id)->exists();
        $isStaff = in_array($user->role, ['coordinator', 'admin', 'vparttcs']);

        if (!$isPi && !$isTm && !$isStaff) {
            abort(403, 'You are not authorized to upload documents to this project.');
        }

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $mimeType = $file->getClientMimeType();
        $fileSize = $file->getSize();

        // Check for existing document with same title & type to increment version
        $existing = ProjectDocument::where('project_id', $projectId)
            ->where('title', $request->title)
            ->where('document_type', $request->document_type)
            ->where('is_current', true)
            ->first();

        $versionNumber = 1;
        if ($existing) {
            $existing->update(['is_current' => false]);
            $versionNumber = $existing->version_number + 1;
        }

        $path = $file->store("project_documents/{$projectId}", 'public');

        $doc = ProjectDocument::create([
            'project_id'     => $projectId,
            'uploaded_by'    => $user->id,
            'title'          => $request->title,
            'document_type'  => $request->document_type,
            'file_path'      => $path,
            'file_name'      => $originalName,
            'mime_type'      => $mimeType,
            'file_size'      => $fileSize,
            'version_number' => $versionNumber,
            'is_current'     => true,
            'notes'          => $request->notes,
        ]);

        AuditService::log(
            'DOCUMENT_UPLOADED',
            'ProjectDocument',
            $doc->id,
            "Uploaded v{$versionNumber} of '{$doc->title}' ({$doc->document_type}) for Project #{$projectId}"
        );

        return back()->with('success', "Document '{$doc->title}' (v{$versionNumber}) uploaded successfully.");
    }

    /**
     * Download or stream a project document with access control.
     */
    public function download(int $id)
    {
        $document = ProjectDocument::with('project')->findOrFail($id);
        $user = Auth::user();
        $project = $document->project;

        // Access control: project participants or institutional managers
        $isPi = (int) $project->pi_id === (int) $user->id;
        $isTm = $project->teamMembers()->where('user_id', $user->id)->exists();
        $isManager = in_array($user->role, ['dh', 'coordinator', 'dean', 'rcsc', 'vparttcs', 'finance', 'admin', 'reviewer']);

        if (!$isPi && !$isTm && !$isManager) {
            abort(403, 'Unauthorized access to project document.');
        }

        if (!Storage::disk('public')->exists($document->file_path)) {
            return back()->with('error', 'Document file is missing from storage.');
        }

        AuditService::log(
            'DOCUMENT_DOWNLOADED',
            'ProjectDocument',
            $document->id,
            "User {$user->name} downloaded '{$document->file_name}' (Project #{$project->project_id})"
        );

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    /**
     * Delete a project document.
     */
    public function destroy(int $id): RedirectResponse
    {
        $document = ProjectDocument::with('project')->findOrFail($id);
        $user = Auth::user();

        if ((int) $document->project->pi_id !== (int) $user->id && $user->role !== 'admin') {
            abort(403, 'Only the PI or Administrator can delete project documents.');
        }

        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $title = $document->title;
        $document->delete();

        // If a prior version exists, promote the latest remaining one to current
        $latestRemaining = ProjectDocument::where('project_id', $document->project_id)
            ->where('title', $title)
            ->latest('version_number')
            ->first();

        if ($latestRemaining) {
            $latestRemaining->update(['is_current' => true]);
        }

        AuditService::log('DOCUMENT_DELETED', 'ProjectDocument', $id, "Deleted document '{$title}'");

        return back()->with('success', "Document '{$title}' deleted successfully.");
    }
}
