<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-semibold">Task Manager</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">Welcome, {{ auth()->user()->name }}!</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-red-600 hover:text-red-800">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-sm font-medium text-gray-500">Total Tasks</h3>
                <p class="text-2xl font-bold text-gray-900">{{ $totalTasks ?? 0 }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-sm font-medium text-gray-500">Completed</h3>
                <p class="text-2xl font-bold text-green-600">{{ $completedTasks ?? 0 }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-sm font-medium text-gray-500">Pending</h3>
                <p class="text-2xl font-bold text-yellow-600">{{ $pendingTasks ?? 0 }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-sm font-medium text-gray-500">High Priority</h3>
                <p class="text-2xl font-bold text-red-600">{{ $highPriorityTasks ?? 0 }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Add Task Form -->
            <div class="lg:col-span-1">
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-lg font-medium mb-4">Add New Task</h2>
                    <form method="POST" action="{{ route('tasks.store') }}">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                            <input type="text" name="title" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                            <select name="priority" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                            <input type="datetime-local" name="due_date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Add Task
                        </button>
                    </form>
                </div>
            </div>

            <!-- Tasks List -->
            <div class="lg:col-span-2">
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-medium">Your Tasks</h2>
                        <div class="flex space-x-2">
                            <a href="{{ route('dashboard') }}" class="px-3 py-1 text-sm bg-gray-200 rounded {{ !request('filter') ? 'bg-blue-500 text-white' : '' }}">All</a>
                            <a href="{{ route('dashboard', ['filter' => 'pending']) }}" class="px-3 py-1 text-sm bg-gray-200 rounded {{ request('filter') == 'pending' ? 'bg-blue-500 text-white' : '' }}">Pending</a>
                            <a href="{{ route('dashboard', ['filter' => 'completed']) }}" class="px-3 py-1 text-sm bg-gray-200 rounded {{ request('filter') == 'completed' ? 'bg-blue-500 text-white' : '' }}">Completed</a>
                            <a href="{{ route('dashboard', ['filter' => 'high']) }}" class="px-3 py-1 text-sm bg-gray-200 rounded {{ request('filter') == 'high' ? 'bg-blue-500 text-white' : '' }}">High Priority</a>
                        </div>
                    </div>

                    @if(isset($tasks) && $tasks->count() > 0)
                        <div class="space-y-3">
                            @foreach($tasks as $task)
                                <div class="border rounded-lg p-4 {{ $task->completed ? 'bg-green-50' : 'bg-white' }}">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2">
                                                <h3 class="font-medium {{ $task->completed ? 'line-through text-gray-500' : '' }}">
                                                    {{ $task->title }}
                                                </h3>
                                                <span class="px-2 py-1 text-xs rounded-full
                                                    {{ $task->priority === 'high' ? 'bg-red-100 text-red-800' : '' }}
                                                    {{ $task->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                    {{ $task->priority === 'low' ? 'bg-green-100 text-green-800' : '' }}">
                                                    {{ ucfirst($task->priority) }}
                                                </span>
                                            </div>
                                            @if($task->description)
                                                <p class="text-gray-600 text-sm mt-1">{{ $task->description }}</p>
                                            @endif
                                            @if($task->due_date)
                                                <p class="text-gray-500 text-sm mt-1">Due: {{ $task->due_date->format('M d, Y H:i') }}</p>
                                            @endif
                                        </div>
                                        <div class="flex space-x-2">
                                            <form method="POST" action="{{ route('tasks.toggle', $task) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-sm px-3 py-1 rounded {{ $task->completed ? 'bg-yellow-500 text-white' : 'bg-green-500 text-white' }}">
                                                    {{ $task->completed ? 'Undo' : 'Complete' }}
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('tasks.destroy', $task) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-sm px-3 py-1 bg-red-500 text-white rounded" onclick="return confirm('Are you sure?')">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500">No tasks found. Create your first task!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>
