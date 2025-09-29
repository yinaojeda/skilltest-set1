<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TaskAssignmentService;
use Illuminate\Support\Facades\Cache;
use App\Models\Task;
use App\Models\Project;
use App\Http\Requests\StoreTaskRequest;

/**
 * Controller for managing tasks within projects.
 */
class TaskController extends Controller
{
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
    public function store(StoreTaskRequest $request, Project $project, TaskAssignmentService $service)

    {
        $data = $request->validated();

        $task = Task::create([
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'status'      => $data['status'] ?? 'pending',
            'due_date'    => $data['due_date'] ?? null,
            'project_id'  => $project->id,
            'assigned_to' => null,
        ]);

        $service->assign($task, $data);
        Cache::tags('projects', 'tasks')->flush();

        return response()->json(['data' => $task], 201);
    }

    /**
     * Update the specified task.
     */
    public function update(Request $request, Task $task, TaskAssignmentService $service)
    {
        $user = $request->user();

        if (! ($user->isAdmin() || $user->isManager() || $user->id === $task->assigned_to)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'title'        => 'sometimes|string|max:255',
            'description'  => 'sometimes|nullable|string',
            'status'       => 'sometimes|in:pending,in-progress,done',
            'due_date'     => 'sometimes|nullable|date',
            'assigned_to'  => 'sometimes|exists:users,id',
        ]);

        $service->assign($task, $data); // pass the validated array
        Cache::tags('tasks')->flush();
        Cache::tags('projects')->flush();

        return response()->json(['data' => $task]);
    }

    /**
     * Remove the specified task.
     */
    public function destroy(Task $task)
    {
        $task->delete();
        Cache::tags('tasks')->flush();
        Cache::tags('projects')->flush();
        return response()->json(['message' => 'Deleted']);
    }
}
