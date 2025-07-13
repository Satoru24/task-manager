<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{

   public function index()
    {
        $user = auth()->user();
        $query = $user->tasks();

        // Apply filters
        if (request('filter') == 'completed') {
            $query->where('completed', true);
        } elseif (request('filter') == 'pending') {
            $query->where('completed', false);
        } elseif (request('filter') == 'high') {
            $query->where('priority', 'high');
        }

        $tasks = $query->latest()->get();

        // Calculate stats
        $totalTasks = $user->tasks()->count();
        $completedTasks = $user->tasks()->where('completed', true)->count();
        $pendingTasks = $user->tasks()->where('completed', false)->count();
        $highPriorityTasks = $user->tasks()->where('priority', 'high')->count();

        return view('dashboard', compact('tasks', 'totalTasks', 'completedTasks', 'pendingTasks', 'highPriorityTasks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date',
        ]);

        auth()->user()->tasks()->create($validated);

        return redirect()->route('dashboard')->with('success', 'Task created successfully!');
    }

    public function toggle(Task $task)
    {
        $this->authorize('update', $task);

        $task->update(['completed' => !$task->completed]);

        return redirect()->route('dashboard')->with('success', 'Task updated successfully!');
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $task->delete();

        return redirect()->route('dashboard')->with('success', 'Task deleted successfully!');
    }
}
