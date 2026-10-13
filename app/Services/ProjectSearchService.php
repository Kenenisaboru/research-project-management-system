<?php

namespace App\Services;

use App\Models\Department;
use App\Models\Evaluation;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Class ProjectSearchService
 *
 * Provides full-text searching, multi-criteria filtering, role-scoped queries,
 * keyword highlighting, and direct CSV/PDF export of research project datasets.
 */
class ProjectSearchService
{
    /**
     * Build role-scoped and filtered Project query.
     *
     * @param  Request  $request
     * @param  User|null  $user
     * @return Builder
     */
    public static function buildQuery(Request $request, ?User $user = null): Builder
    {
        $query = Project::with(['pi', 'thematicArea', 'department', 'irercClearance']);

        // 1. Role-based scoping
        if ($user) {
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
        }

        // 2. Keyword Search across Title, Abstract, Code
        $keyword = $request->input('search') ?: $request->input('keyword');
        if (!empty($keyword)) {
            $query->where(function (Builder $q) use ($keyword) {
                $q->where('title', 'LIKE', "%{$keyword}%")
                  ->orWhere('abstract_text', 'LIKE', "%{$keyword}%")
                  ->orWhere('project_code', 'LIKE', "%{$keyword}%");
            });
        }

        // 3. PI Name Filter
        if ($request->filled('pi_name')) {
            $piSearch = $request->input('pi_name');
            $query->whereHas('pi', function ($q) use ($piSearch) {
                $q->where('name', 'LIKE', "%{$piSearch}%");
            });
        }

        // 4. Status Filter
        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        // 5. Thematic Area Filter (supports both thematic_area and thematic_id)
        $thematicId = $request->input('thematic_id') ?: $request->input('thematic_area');
        if (!empty($thematicId) && $thematicId !== 'all') {
            $query->where('thematic_id', $thematicId);
        }

        // 6. Department Filter
        if ($request->filled('dept_id') && $request->input('dept_id') !== 'all') {
            $query->where('dept_id', $request->input('dept_id'));
        }

        // 7. Budget Range Filters
        if ($request->filled('min_budget')) {
            $query->where('requested_budget', '>=', (float) $request->input('min_budget'));
        }
        if ($request->filled('max_budget')) {
            $query->where('requested_budget', '<=', (float) $request->input('max_budget'));
        }

        // 8. Date Range Filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        // 9. Sorting
        $sort = $request->input('sort_by', 'newest');
        match ($sort) {
            'oldest'      => $query->oldest('created_at'),
            'budget_desc' => $query->orderByDesc('requested_budget'),
            'budget_asc'  => $query->orderBy('requested_budget'),
            'title_asc'   => $query->orderBy('title'),
            'title_desc'  => $query->orderByDesc('title'),
            default       => $query->latest('created_at'),
        };

        return $query;
    }

    /**
     * Highlight matching keyword occurrences in text string.
     *
     * @param  string|null  $text
     * @param  string|null  $keyword
     * @return string
     */
    public static function highlight(?string $text, ?string $keyword): string
    {
        if (empty($text) || empty($keyword)) {
            return (string) $text;
        }

        $escaped = preg_quote(trim($keyword), '/');
        return preg_replace('/(' . $escaped . ')/i', '<mark class="bg-warning-subtle text-dark px-1 rounded">$1</mark>', e($text));
    }

    /**
     * Stream search results as clean CSV.
     *
     * @param  \Illuminate\Support\Collection  $projects
     * @param  string  $filename
     * @return StreamedResponse
     */
    public static function exportCsv($projects, string $filename = 'UNI_Project_Search_Results.csv'): StreamedResponse
    {
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($projects) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Project Code',
                'Title',
                'Principal Investigator',
                'Department',
                'Thematic Area',
                'Status',
                'Requested Budget (ETB)',
                'Approved Budget (ETB)',
                'Submission Date',
            ]);

            foreach ($projects as $p) {
                fputcsv($handle, [
                    $p->project_code ?? ('UNI-PRJ-' . $p->project_id),
                    $p->title,
                    $p->pi->name ?? 'N/A',
                    $p->department->name ?? 'N/A',
                    $p->thematicArea->title ?? 'General',
                    str_replace('_', ' ', $p->status),
                    number_format((float) $p->requested_budget, 2, '.', ''),
                    number_format((float) ($p->approved_budget ?? 0), 2, '.', ''),
                    $p->created_at ? $p->created_at->format('Y-m-d') : 'N/A',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
