<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Task Manager</h1>

        <form id="taskForm" class="flex mb-4">
            <input type="text" id="title" placeholder="Task title" class="border p-2 flex-grow mr-2">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Add Task</button>
        </form>

        <ul id="taskList" class="space-y-2"></ul>
    </div>

    <script>
        const apiBase = 'http://127.0.0.1:8000/api/tasks';
        const taskList = document.getElementById('taskList');
        const taskForm = document.getElementById('taskForm');
        const titleInput = document.getElementById('title');

        // Load tasks
        async function loadTasks() {
            const res = await fetch(apiBase);
            const tasks = await res.json();
            taskList.innerHTML = '';
            tasks.forEach(task => {
                const li = document.createElement('li');
                li.className = 'flex justify-between items-center p-2 border rounded';
                li.innerHTML = `
                    <span class="${task.is_completed ? 'line-through text-gray-500' : ''}">${task.title}</span>
                    <div class="space-x-2">
                        <button onclick="toggleComplete(${task.id}, ${task.is_completed})" class="text-green-500">✔️</button>
                        <button onclick="deleteTask(${task.id})" class="text-red-500">🗑️</button>
                    </div>
                `;
                taskList.appendChild(li);
            });
        }

        // Add task
        taskForm.addEventListener('submit', async e => {
            e.preventDefault();
            await fetch(apiBase, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ title: titleInput.value })
            });
            titleInput.value = '';
            loadTasks();
        });

        // Toggle complete
        async function toggleComplete(id, currentStatus) {
            await fetch(`${apiBase}/${id}`, {
                method: 'PUT',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ is_completed: !currentStatus })
            });
            loadTasks();
        }

        // Delete task
        async function deleteTask(id) {
            await fetch(`${apiBase}/${id}`, { method: 'DELETE' });
            loadTasks();
        }

        // Initial load
        loadTasks();
    </script>
</body>
</html>
