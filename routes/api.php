<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CommentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

Route::middleware('auth:sanctum', 'log.request')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // Admin → Projects CRUD
    Route::middleware('role:admin')->group(function () {
        Route::post('/projects',              [ProjectController::class, 'store']);
        Route::put('/projects/{project}',     [ProjectController::class, 'update']);
        Route::delete('/projects/{project}',  [ProjectController::class, 'destroy']);
    });
    // Anyone authenticated can view projects:
    Route::get('/projects',          [ProjectController::class, 'index']);
    Route::get('/projects/{project}', [ProjectController::class, 'show']);

    // Manager → Tasks CRUD (create/delete here; update has extra rule for assignee below)
    Route::middleware('role:manager')->group(function () {
        Route::post('/projects/{project}/tasks', [TaskController::class, 'store']);
        Route::delete('/tasks/{task}',           [TaskController::class, 'destroy']);
    });
    // Read tasks
    Route::get('/projects/{project}/tasks', [TaskController::class, 'index']);
    Route::get('/tasks/{task}',             [TaskController::class, 'show']);

    // Update task: allow managers via route middleware OR assignee via controller check
    Route::put('/tasks/{task}', [TaskController::class, 'update']); // controller will enforce

    // User → Comments & assigned tasks
    // Everyone authenticated can attempt, but controller will enforce visibility (assigned or manager/admin)
    Route::get('/tasks/{task}/comments',  [CommentController::class, 'index']);
    Route::post('/tasks/{task}/comments', [CommentController::class, 'store']);
});
