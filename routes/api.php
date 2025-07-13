<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TaskController;

/Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('tasks', TaskController::class);
    Route::patch('tasks/{task}/toggle', [TaskController::class, 'toggle']);
});
