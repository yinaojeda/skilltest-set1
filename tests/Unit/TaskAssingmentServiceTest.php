<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskAssignmentService;
use App\Notifications\TaskAssignedNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Unit test for TaskAssignmentService::create
 */
it('creates a task and notifies the assignee', function () {
    Notification::fake();

    $project = Project::factory()->create();
    $user = User::factory()->create();

    $service = new TaskAssignmentService();

    $data = [
        'title'       => 'New Task',
        'description' => 'Test desc',
        'assigned_to' => $user->id,
    ];

    $task = $service->create($project, $data);

    expect($task)->toBeInstanceOf(Task::class)
        ->and($task->title)->toBe('New Task');

    Notification::assertSentTo($user, TaskAssignedNotification::class);
});

/**
 * Unit test for TaskAssignmentService::update
 */
it('reassigns a task and notifies the new assignee', function () {
    Notification::fake();

    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $project = Project::factory()->create();
    $task = Task::factory()->for($project)->create(['assigned_to' => $user1->id]);

    $service = new TaskAssignmentService();

    $updatedTask = $service->update($task, ['assigned_to' => $user2->id]);

    expect($updatedTask->assigned_to)->toBe($user2->id);

    Notification::assertSentTo($user2, TaskAssignedNotification::class);
});
