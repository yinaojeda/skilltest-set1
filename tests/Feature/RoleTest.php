<?php

use App\Models\User;
use App\Models\Project;
use App\Models\Task;

/**
 * Test role-based access control functionalities.
 */
it('allows manager to create a task', function () {
    $manager = User::factory()->create(['role' => 'manager']);
    $token = $manager->createToken('api')->plainTextToken;

    $project = Project::factory()->create(['created_by' => $manager->id]);

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->postJson("/api/projects/{$project->id}/tasks", [
            'title'       => 'My Task',
            'description' => 'Desc',
            'assigned_to' => $manager->id,
        ]);

    $response->assertStatus(201);
});

it('allows user to create a comment', function () {
    $user = User::factory()->create(['role' => 'user']);
    $token = $user->createToken('api')->plainTextToken;

    $task = Task::factory()->create(['assigned_to' => $user->id]);

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->postJson("/api/tasks/{$task->id}/comments", [
            'body' => 'My Comment'
        ]);

    $response->assertStatus(201);
});

it('lists projects', function () {
    $user = User::factory()->create();
    $token = $user->createToken('api')->plainTextToken;

    $this->withHeader('Authorization', 'Bearer ' . $token)
        ->getJson('/api/projects')
        ->assertStatus(200);
});
