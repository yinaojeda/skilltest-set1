<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TaskAssignmentService;
use Illuminate\Support\Facades\Cache;
use App\Models\Task;
use App\Models\Project;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;

/**
 * Controller for managing tasks within projects.
 */
class TaskController extends Controller
{
    private TaskAssignmentService $service;

    public function __construct(TaskAssignmentService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of tasks for a specific project with optional filtering and searching.
     */
    public function index(Project $project, Request $request)
    {
        $status = $request->query('status');
        $search = $request->query('q');

        $tasks = $project->tasks()
            ->filterByStatus($status)
            ->searchByTitle($search)
            ->get();

        return response()->json(['data' => $tasks]);
    }

    /**
     * Display the specified task along with its comments and assigned user.
     */
    public function show(Task $task)
    {
        return response()->json(['data' => $task->load('comments', 'assignedTo')]);
    }

    /**
     * Store a newly created task in a specific project.
     */
    public function store(StoreTaskRequest $request, Project $project)
    {
        $task = $this->service->create($project, $request->validated());

        Cache::tags(['projects', 'tasks'])->flush();

        return response()->json(['data' => $task], 201);
    }

    /**
     * Update the specified task.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $user = $request->user();

        if (! ($user->isAdmin() || $user->isManager() || $user->id === $task->assigned_to)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $task = $this->service->update($task, $request->validated());

        Cache::tags(['tasks', 'projects'])->flush();

        return response()->json(['data' => $task]);
    }

    /**
     * Remove the specified task.
     */
    public function destroy(Task $task)
    {
        $task->delete();
        Cache::tags(['tasks', 'projects'])->flush();
        return response()->json(['message' => 'Deleted']);
    }
}
