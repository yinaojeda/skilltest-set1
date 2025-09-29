<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use App\Notifications\TaskAssignedNotification;
use Illuminate\Support\Facades\Validator;

/**
 * Service class for handling task assignments and notifications.
 */
class TaskAssignmentService
{
    /**
     * Assign or reassign a task to a user and notify them.
     *
     * @param Task  $task The task to be assigned.
     * @param array $data The data containing assignment details.
     * @return Task The updated task instance.
     */
    public function assign(Task $task, array $data): Task
    {
        // Validate incoming data
        Validator::make($data, [
            'title'        => 'sometimes|string|max:255',
            'description'  => 'sometimes|nullable|string',
            'status'       => 'sometimes|in:pending,in-progress,done,completed',
            'due_date'     => 'sometimes|nullable|date',
            'assigned_to'  => 'sometimes|nullable|exists:users,id',
        ])->validate();

        $reassigned = array_key_exists('assigned_to', $data) && $data['assigned_to'] !== $task->assigned_to;

        $task->update($data);

        // Notify only if the assignee changed
        if ($reassigned && $task->assigned_to) {
            $assignee = User::find($task->assigned_to);
            if ($assignee) {
                $assignee->notify(new TaskAssignedNotification($task));
            }
        }

        return $task;
    }

    /**
     * Create a new task under a project with given data.
     * Sends a notification if created with an assignee.
     */
    public function create(Project $project, array $data): Task
    {
        // Optional: validate here too
        Validator::make($data, [
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'status'       => 'nullable|in:pending,in-progress,done,completed',
            'due_date'     => 'nullable|date',
            'assigned_to'  => 'nullable|exists:users,id',
        ])->validate();

        $task = $project->tasks()->create($data);

        // Notify immediately if assigned_to is present
        if (!empty($data['assigned_to'])) {
            $assignee = User::find($data['assigned_to']);
            if ($assignee) {
                $assignee->notify(new TaskAssignedNotification($task));
            }
        }

        return $task;
    }

    /**
     * Update an existing task with given data.
     */
    public function update(Task $task, array $data): Task
    {
        return $this->assign($task, $data); // reuse the assign logic
    }
}
