<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use Illuminate\Support\Facades\Cache;

/**
 * Controller for managing projects.
 */
class ProjectController extends Controller
{
    /**
     * Display a listing of projects with optional filtering and searching.
     */
    public function index(Request $request)
    {
        $status = $request->query('status');
        $search = $request->query('q');

        $cacheKey = 'projects_list_' . md5(($status ?? '') . '|' . ($search ?? ''));

        $projects = Cache::tags('projects')->remember($cacheKey, 60, function () use ($status, $search) {
            return \App\Models\Project::with('tasks')
                ->filterByStatus($status)
                ->searchByTitle($search)
                ->get();
        });

        return response()->json(['data' => $projects]);
    }

    /**
     * Display project details along with its tasks.
     */
    public function show(Project $project)
    {
        return response()->json(['data' => $project->load('tasks')]);
    }

    /**
     * Store a new project (admin and manager only).
     */
    public function store(Request $request, Project $project)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
        ]);

        $data['created_by'] = $request->user()->id;

        $project = Project::create($data);

        Cache::tags('projects')->flush();

        return response()->json(['data' => $project], 201);
    }

    /**
     * Update an existing project (admin and manager only).
     */
    public function update(Request $request, Project $project)
    {
        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
        ]);
        $project->update($data);

        Cache::tags('projects')->flush();

        return response()->json(['data' => $project]);
    }

    /**
     * Delete a project (admin and manager only).
     */
    public function destroy(Project $project)
    {
        $project->delete();

        Cache::tags('projects')->flush();

        return response()->json(['message' => 'Deleted']);
    }
}
