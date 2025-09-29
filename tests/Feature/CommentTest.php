<?php
use App\Models\User;
use App\Models\Task;
/**
 * Test adding comments to tasks.
 */
it('adds a comment to a task', function () {
    $user = User::factory()->create(['role'=>'user']);
    $token = $user->createToken('api')->plainTextToken;
    $task = Task::factory()->create(['assigned_to'=>$user->id]);

    $response = $this->withHeader('Authorization','Bearer '.$token)
        ->postJson("/api/tasks/{$task->id}/comments", ['body'=>'My Comment']);

    $response->assertStatus(201)
             ->assertJsonPath('data.body','My Comment');
});
