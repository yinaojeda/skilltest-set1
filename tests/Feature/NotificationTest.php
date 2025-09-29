<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssignedNotification;
use Illuminate\Support\Facades\Notification;

/**
 * Test notification functionalities, specifically task assignment notifications.
 */
it('queues a notification when a task is created with an assignee', function () {
    Notification::fake();

    $manager = User::factory()->create(['role' => 'manager']);
    $token = $manager->createToken('api')->plainTextToken;

    $assignee = User::factory()->create();
    $project = Project::factory()->create(['created_by' => $manager->id]);

    $this->withHeader('Authorization', 'Bearer ' . $token)
        ->postJson("/api/projects/{$project->id}/tasks", [
            'title' => 'N1',
            'description' => 'D1',
            'assigned_to' => $assignee->id,
        ])
        ->assertStatus(201);

    Notification::assertSentTo($assignee, TaskAssignedNotification::class);
});
