<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Comment;
use App\Models\Task;    

/**
 * Controller for managing comments on tasks.
 */
class CommentController extends Controller
{
    /**
     * Display a listing of comments for a specific task.
     */
    public function index(Task $task)
    {
        $user = request()->user();

        if (! ($user->isAdmin() || $user->isManager() || $user->id === $task->assigned_to)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $comments = Comment::with('user')
            ->where('task_id', $task->id)
            ->latest()
            ->paginate(10);

        return response()->json(['data' => $comments]);
    }

    /**
     * Store a new comment on a task.
     */
    public function store(Request $request, Task $task)
    {
        $user = $request->user();

        if (! ($user->isAdmin() || $user->isManager() || $user->id === $task->assigned_to)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $comment = Comment::create([
            'task_id' => $task->id,
            'user_id' => $user->id,
            'body'    => $data['body'],
        ]);

        Cache::tags('comments')->flush();

        return response()->json(['data' => $comment], 201);
    }
}
