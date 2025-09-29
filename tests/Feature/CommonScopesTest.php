<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\User;

/**
 * Test common query scopes for models.
 */

beforeEach(function () {
    // create a user to authenticate requests
    $this->user = User::factory()->create();
    $this->token = $this->user->createToken('api')->plainTextToken;
});

it('searches projects by title scope', function () {
    $project1 = Project::factory()->create(['title' => 'My Big Project']);
    $project2 = Project::factory()->create(['title' => 'Another One']);

    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->getJson('/api/projects?q=Big');

    $response->assertStatus(200)
        ->assertJsonFragment(['title' => 'My Big Project'])
        ->assertJsonMissing(['title' => 'Another One']);
});

it('filters tasks by status scope inside project', function () {
    $project = Project::factory()->create(['created_by' => $this->user->id]);

    $task1 = Task::factory()->create(['project_id' => $project->id, 'title' => 'Pending Task', 'status' => 'pending']);
    $task2 = Task::factory()->create(['project_id' => $project->id, 'title' => 'Done Task', 'status' => 'done']);

    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->getJson("/api/projects/{$project->id}/tasks?status=pending");

    $response->assertStatus(200)
        ->assertJsonFragment(['title' => 'Pending Task'])
        ->assertJsonMissing(['title' => 'Done Task']);
});
