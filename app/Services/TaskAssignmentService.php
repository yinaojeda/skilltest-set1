<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Notifications\TaskAssignedNotification;

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
        // Optional: extra validation here
        Validator::make($data, [
            'title'        => 'sometimes|string|max:255',
            'description'  => 'sometimes|nullable|string',
            'status'       => 'sometimes|in:pending,in-progress,done',
            'due_date'     => 'sometimes|nullable|date',
            'assigned_to'  => 'sometimes|exists:users,id',
        ])->validate();

        $reassigned = array_key_exists('assigned_to', $data) && $data['assigned_to'] !== $task->assigned_to;


        $task->update($data);

        if ($reassigned && $task->assigned_to) {
            $assignee = User::find($task->assigned_to);
            if ($assignee) {
                $assignee->notify(new TaskAssignedNotification($task));
            }
        }

        return $task;
    }
}
