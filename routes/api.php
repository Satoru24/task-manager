<?php

use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes (no authentication required)
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

// Protected routes (authentication required)
Route::middleware('auth:sanctum')->group(function () {

    // Authentication routes
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'user']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });

    // Task management routes
    Route::prefix('tasks')->group(function () {
        Route::get('/', [TaskController::class, 'index']);
        Route::post('/', [TaskController::class, 'store']);
        Route::get('stats', [TaskController::class, 'stats']);
        Route::get('search/{query}', [TaskController::class, 'search']);
        Route::get('{task}', [TaskController::class, 'show']);
        Route::put('{task}', [TaskController::class, 'update']);
        Route::delete('{task}', [TaskController::class, 'destroy']);
        Route::patch('{task}/toggle', [TaskController::class, 'toggle']);
    });

    // Alternative resourceful route (you can use this instead of manual routes above)
    // Route::apiResource('tasks', TaskController::class);
    // Route::patch('tasks/{task}/toggle', [TaskController::class, 'toggle']);
    // Route::get('tasks/search/{query}', [TaskController::class, 'search']);
    // Route::get('tasks-stats', [TaskController::class, 'stats']);
});

// Fallback route for undefined API endpoints
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found'
    ], 404);
});

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {

    // Task CRUD routes
    Route::apiResource('tasks', TaskController::class);

    // Additional task routes
    Route::patch('tasks/{task}/complete', [TaskController::class, 'markAsCompleted']);
    Route::get('tasks-stats', [TaskController::class, 'stats']);

});
