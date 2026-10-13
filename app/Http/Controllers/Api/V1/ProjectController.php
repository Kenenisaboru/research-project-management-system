<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\ProjectStoreRequest;
use App\Http\Requests\ProjectUpdateRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Department;
use App\Models\Evaluation;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class ProjectController
 *
 * Provides RESTful JSON API operations for research proposals and projects.
 */
class ProjectController extends ApiController
{
    /**
     * List projects accessible by the authenticated user with optional filtering.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Project::with(['pi', 'thematicArea', 'department', 'irercClearance']);

        if ($user->role === 'pi') {
            $query->where('pi_id', $user->id);
        } elseif ($user->role === 'tm') {
            $teamProjectIds = ProjectMember::where('user_id', $user->id)->pluck('project_id')->toArray();
            $query->whereIn('project_id', $teamProjectIds);
        } elseif ($user->role === 'reviewer') {
            $assignedProjectIds = Evaluation::where('examiner_id', $user->id)->pluck('project_id')->toArray();
            $query->whereIn('project_id', $assignedProjectIds);
        } elseif ($user->role === 'dh') {
            $query->where('projects.dept_id', $user->dept_id)
                  ->where('status', '!=', 'Draft');
        } elseif (in_array($user->role, ['coordinator', 'dean'])) {
            $collegeId = Department::where('id', $user->dept_id)->value('college_id');
            $collegeDeptIds = Department::where('college_id', $collegeId)->pluck('id')->toArray();
            $query->whereIn('projects.dept_id', $collegeDeptIds)
                  ->where('status', '!=', 'Draft');
        } else {
            $query->where('status', '!=', 'Draft');
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('title', 'LIKE', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('thematic_area')) {
            $query->where('thematic_id', $request->input('thematic_area'));
        }

        $perPage = min((int) $request->input('per_page', 15), 50);
        $paginated = $query->latest()->paginate($perPage);

        return $this->success([
            'items'      => ProjectResource::collection($paginated->items()),
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'per_page'     => $paginated->perPage(),
                'total'        => $paginated->total(),
            ],
        ], 'Projects retrieved successfully.');
    }

    /**
     * Show details of a specific project.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $project = Project::with(['pi', 'thematicArea', 'department', 'members.user', 'evaluations', 'budgetRequests'])
            ->find($id);

        if (!$project) {
            return $this->error('Project record not found.', null, 404);
        }

        $user = $request->user();
        if ($user->role === 'pi' && (int) $project->pi_id !== (int) $user->id && $project->status === 'Draft') {
            return $this->error('Access denied to private draft proposal.', null, 403);
        }

        return $this->success(new ProjectResource($project), 'Project details retrieved successfully.');
    }

    /**
     * Store a new proposal via API.
     *
     * @param  ProjectStoreRequest  $request
     * @return JsonResponse
     */
    public function store(ProjectStoreRequest $request): JsonResponse
    {
        $user = $request->user();
        $docPath = null;

        if ($request->hasFile('proposal_document')) {
            $docPath = $request->file('proposal_document')->store('proposals', 'public');
        }

        $project = Project::create([
            'title'                 => $request->title,
            'abstract_text'         => $request->abstract_text,
            'thematic_id'           => $request->thematic_id,
            'pi_id'                 => $user->id,
            'dept_id'               => $user->dept_id,
            'requested_budget'      => $request->requested_budget,
            'status'                => 'Draft',
            'current_stage'         => 0,
            'proposal_document_url' => $docPath,
        ]);

        AuditService::log('API_PROJECT_CREATE', 'Project', $project->project_id, "PI created draft proposal '{$project->title}' via API");

        return $this->success(
            new ProjectResource($project->load(['thematicArea', 'department', 'pi'])),
            'Research proposal draft created successfully.',
            201
        );
    }

    /**
     * Update an existing proposal via API.
     *
     * @param  ProjectUpdateRequest  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(ProjectUpdateRequest $request, int $id): JsonResponse
    {
        $project = Project::find($id);
        if (!$project) {
            return $this->error('Project record not found.', null, 404);
        }

        $data = [
            'title'            => $request->title,
            'abstract_text'    => $request->abstract_text,
            'thematic_id'      => $request->thematic_id,
            'requested_budget' => $request->requested_budget,
        ];

        if ($request->hasFile('proposal_document')) {
            $docPath = $request->file('proposal_document')->store('proposals', 'public');
            $data['proposal_document_url'] = $docPath;
        }

        $project->update($data);

        AuditService::log('API_PROJECT_UPDATE', 'Project', $project->project_id, "Proposal '{$project->title}' updated via API");

        return $this->success(
            new ProjectResource($project->load(['thematicArea', 'department', 'pi'])),
            'Research proposal updated successfully.'
        );
    }
}
