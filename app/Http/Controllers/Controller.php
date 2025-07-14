<?php

namespace App\Http\Controllers;


use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;


class Controller extends BaseController

{
    use AuthorizesRequests, ValidatesRequests;
    /**
     * Display a listing of the user's tasks.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Auth::user()->tasks();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        // Search by title or description
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Filter by due date
        if ($request->has('due_before')) {
            $query->dueBefore($request->due_before);
        }

        // Show only overdue tasks
        if ($request->has('overdue') && $request->overdue) {
            $query->overdue();
        }

        // Sort by due date, priority, or creation date
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        $validSortFields = ['created_at', 'updated_at', 'due_date', 'priority', 'title'];
        if (in_array($sortBy, $validSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $tasks = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $tasks->items(),
            'pagination' => [
                'current_page' => $tasks->currentPage(),
                'last_page' => $tasks->lastPage(),
                'per_page' => $tasks->perPage(),
                'total' => $tasks->total(),
            ]
        ]);
    }

    /**
     * Store a newly created task.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'status' => ['nullable', Rule::in(Task::getValidStatuses())],
            'priority' => ['nullable', Rule::in(Task::getValidPriorities())],
            'due_date' => 'nullable|date|after:now',
        ]);

        $task = Auth::user()->tasks()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Task created successfully',
            'data' => $task
        ], 201);
    }

    /**
     * Display the specified task.
     */
    public function show(Task $task): JsonResponse
    {
        // Check if the task belongs to the authenticated user
        if ($task->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $task
        ]);
    }

    /**
     * Update the specified task.
     */
    public function update(Request $request, Task $task): JsonResponse
    {
        // Check if the task belongs to the authenticated user
        if ($task->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found'
            ], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'status' => ['sometimes', Rule::in(Task::getValidStatuses())],
            'priority' => ['sometimes', Rule::in(Task::getValidPriorities())],
            'due_date' => 'nullable|date',
        ]);

        $task->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Task updated successfully',
            'data' => $task->fresh()
        ]);
    }

    /**
     * Remove the specified task.
     */
    public function destroy(Task $task): JsonResponse
    {
        // Check if the task belongs to the authenticated user
        if ($task->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found'
            ], 404);
        }

        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Task deleted successfully'
        ]);
    }

    /**
     * Mark a task as completed.
     */
    public function markAsCompleted(Task $task): JsonResponse
    {
        // Check if the task belongs to the authenticated user
        if ($task->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found'
            ], 404);
        }

        $task->markAsCompleted();

        return response()->json([
            'success' => true,
            'message' => 'Task marked as completed',
            'data' => $task->fresh()
        ]);
    }

    /**
     * Get task statistics for the authenticated user.
     */
    public function stats(): JsonResponse
    {
        $user = Auth::user();

        $stats = [
            'total_tasks' => $user->tasks()->count(),
            'pending_tasks' => $user->tasks()->where('status', 'pending')->count(),
            'in_progress_tasks' => $user->tasks()->where('status', 'in_progress')->count(),
            'completed_tasks' => $user->tasks()->where('status', 'completed')->count(),
            'overdue_tasks' => $user->tasks()->where('due_date', '<', now())->where('status', '!=', 'completed')->count(),
            'high_priority_tasks' => $user->tasks()->where('priority', 'high')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
