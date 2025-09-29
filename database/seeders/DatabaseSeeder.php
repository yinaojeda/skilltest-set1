<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\Comment;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        User::truncate();
        Project::truncate();
        Task::truncate();
        Comment::truncate();
        Schema::enableForeignKeyConstraints();

        // create users
        $admins = User::factory()->count(3)->create(['role' => 'admin']);
        $managers = User::factory()->count(3)->create(['role' => 'manager']);
        $users = User::factory()->count(5)->create(['role' => 'user']);

        // Choose a creator for projects (an admin)
        $creator = $admins->first();

        // Projects: exactly 5
        $projects = Project::factory()->count(5)->create([
            'created_by' => $creator->id,
        ]);

        // Tasks: exactly 10 (2 per project) and Comments: exactly 10 (1 per task)
        $allAssignees = $managers->concat($users); // tasks assigned to managers or users

        $taskCount = 0;
        foreach ($projects as $project) {
            for ($i = 0; $i < 2; $i++) { // 2 tasks per project -> 10 total
                $assignee = $allAssignees->random();
                $task = Task::factory()->create([
                    'project_id'  => $project->id,
                    'assigned_to' => $assignee->id,
                ]);
                $taskCount++;

                // 1 comment per task -> 10 comments total
                Comment::factory()->create([
                    'task_id' => $task->id,
                    'user_id' => $assignee->id, // author is the assignee for simplicity
                ]);
            }
        }
    }
}
