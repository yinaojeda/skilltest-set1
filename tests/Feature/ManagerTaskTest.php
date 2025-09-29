<?php

use App\Models\Project;
use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Feature test: Manager can create a task.
 */
it('allows a manager to create a task', function () {
    $manager = User::factory()->manager()->create();
    $project = Project::factory()->create();

    $payload = [
        'title'       => 'Manager Task',
        'description' => 'Task created by manager',
    ];

    $this->actingAs($manager, 'sanctum')
        ->postJson("/api/projects/{$project->id}/tasks", $payload)
        ->assertStatus(201)
        ->assertJsonFragment(['title' => 'Manager Task']);
});

/**
 * Feature test: Manager can update a task.
 */
it('allows a manager to update a task', function () {
    $manager = User::factory()->manager()->create();
    $project = Project::factory()->create();
    $task = Task::factory()->for($project)->create();

    $payload = ['title' => 'Updated by Manager'];

    $this->actingAs($manager, 'sanctum')
        ->putJson("/api/tasks/{$task->id}", $payload)
        ->assertStatus(200)
        ->assertJsonFragment(['title' => 'Updated by Manager']);
});
