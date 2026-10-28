<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\Models\Project;
use App\Models\ThematicArea;
use Illuminate\Http\Request;

class ResearchController extends Controller
{
    /**
     * Public research catalogue. Accessible to all university staff and the
     * research community without authentication. Completed projects are public;
     * in-progress titles/abstracts are shared at title+abstract level only.
     */
    public function catalog(Request $request)
    {
        $publicStatuses = ['Active', 'Ongoing', 'Completed', 'Terminated', 'Rejected'];

        $query = Project::with(['thematicArea', 'department.college'])
            ->whereIn('status', $publicStatuses);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        } else {
            $query->whereIn('status', ['Completed', 'Active', 'Ongoing']);
        }

        if ($request->filled('thematic_id')) {
            $query->where('thematic_id', $request->input('thematic_id'));
        }

        if ($request->filled('college_id')) {
            $query->whereHas('department', fn ($q) => $q->where('college_id', $request->input('college_id')));
        }

        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('abstract_text', 'LIKE', "%{$search}%");
            });
        }

        $projects = $query->latest('approved_at')->paginate(10)->withQueryString();

        $thematicAreas = ThematicArea::where('is_active', true)->orderBy('title')->get();
        $colleges = College::orderBy('name')->get();

        $stats = [
            'completed'  => Project::where('status', 'Completed')->count(),
            'ongoing'    => Project::whereIn('status', ['Active', 'Ongoing'])->count(),
            'terminated' => Project::where('status', 'Terminated')->count(),
            'rejected'   => Project::where('status', 'Rejected')->count(),
        ];

        return view('research.catalog', compact('projects', 'thematicAreas', 'colleges', 'stats'));
    }
}