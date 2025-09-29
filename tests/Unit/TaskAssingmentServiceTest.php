<?php

use App\Models\Task;
use App\Services\TaskAssignmentService;

/**
 * Test TaskAssignmentService functionalities.
 */
it('updates task fields using TaskAssignmentService', function () {
   $task = Task::factory()->create();

    $service = new TaskAssignmentService();

    $service->assign($task, [
        'title' => 'New Title',
        'status' => 'in-progress',
    ]);

    expect($task->refresh()->title)->toBe('New Title')
        ->and($task->status)->toBe('in-progress');      
});