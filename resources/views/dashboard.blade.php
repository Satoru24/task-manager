<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }

        .dashboard {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            color: white;
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }

        .stat-number {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .stat-label {
            font-size: 1.1rem;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .pending { color: #f39c12; }
        .in-progress { color: #3498db; }
        .completed { color: #27ae60; }
        .cancelled { color: #e74c3c; }
        .overdue { color: #e74c3c; }
        .total { color: #9b59b6; }

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .chart-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
        }

        .chart-title {
            font-size: 1.3rem;
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }

        .chart-container {
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pie-chart {
            width: 250px;
            height: 250px;
            position: relative;
        }

        .bar-chart {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: end;
            justify-content: space-around;
            padding: 20px 0;
        }

        .bar {
            width: 60px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            border-radius: 5px 5px 0 0;
            position: relative;
            transition: all 0.3s ease;
        }

        .bar:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .bar-label {
            position: absolute;
            bottom: -25px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 0.9rem;
            color: #666;
        }

        .bar-value {
            position: absolute;
            top: -25px;
            left: 50%;
            transform: translateX(-50%);
            font-weight: bold;
            color: #333;
        }

        .legend {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 20px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 50%;
        }

        .task-list {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
        }

        .task-list h2 {
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }

        .task-item {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
            border-left: 4px solid #3498db;
            transition: transform 0.2s ease;
        }

        .task-item:hover {
            transform: translateX(5px);
        }

        .task-title {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .task-meta {
            font-size: 0.9rem;
            color: #666;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            text-transform: uppercase;
            margin-right: 10px;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-in-progress {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-completed {
            background: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        @media (max-width: 768px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <div class="header">
            <h1>Task Manager Dashboard</h1>
            <p>Overview of your task management system</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number total" id="totalTasks">0</div>
                <div class="stat-label">Total Tasks</div>
            </div>
            <div class="stat-card">
                <div class="stat-number pending" id="pendingTasks">0</div>
                <div class="stat-label">Pending</div>
            </div>
            <div class="stat-card">
                <div class="stat-number in-progress" id="inProgressTasks">0</div>
                <div class="stat-label">In Progress</div>
            </div>
            <div class="stat-card">
                <div class="stat-number completed" id="completedTasks">0</div>
                <div class="stat-label">Completed</div>
            </div>
            <div class="stat-card">
                <div class="stat-number cancelled" id="cancelledTasks">0</div>
                <div class="stat-label">Cancelled</div>
            </div>
            <div class="stat-card">
                <div class="stat-number overdue" id="overdueTasks">0</div>
                <div class="stat-label">Overdue</div>
            </div>
        </div>

        <div class="charts-grid">
            <div class="chart-card">
                <h3 class="chart-title">Task Status Distribution</h3>
                <div class="chart-container">
                    <canvas id="statusChart" width="300" height="300"></canvas>
                </div>
                <div class="legend" id="statusLegend"></div>
            </div>

            <div class="chart-card">
                <h3 class="chart-title">Priority Distribution</h3>
                <div class="chart-container">
                    <div class="bar-chart" id="priorityChart"></div>
                </div>
            </div>
        </div>

        <div class="task-list">
            <h2>Recent Tasks</h2>
            <div id="taskList"></div>
        </div>
    </div>

    <script>
        // Sample data based on your Laravel output
        const taskData = {
            total: 50,
            pending: 14,
            inProgress: 12,
            completed: 13,
            cancelled: 11,
            overdue: 0,
            priorities: {
                high: 15,
                medium: 22,
                low: 13
            },
            recentTasks: [
                { title: "Ratione tenetur ut quia", status: "in_progress", priority: "medium", dueDate: "2025-07-28" },
                { title: "Laudantium nam nesciunt impedit", status: "in_progress", priority: "high", dueDate: "2025-07-26" },
                { title: "Ut qui qui natus", status: "pending", priority: "medium", dueDate: "2025-07-26" },
                { title: "Voluptatem illum et quasi", status: "pending", priority: "high", dueDate: "2025-08-01" },
                { title: "Ut optio iusto", status: "completed", priority: "medium", dueDate: "2025-08-03" },
                { title: "Voluptatem ut et", status: "in_progress", priority: "high", dueDate: "2025-07-28" },
                { title: "Necessitatibus rerum ut", status: "pending", priority: "medium", dueDate: null },
                { title: "Dolorum sit non consequatur", status: "completed", priority: "medium", dueDate: "2025-07-17" }
            ]
        };

        // Update statistics
        document.getElementById('totalTasks').textContent = taskData.total;
        document.getElementById('pendingTasks').textContent = taskData.pending;
        document.getElementById('inProgressTasks').textContent = taskData.inProgress;
        document.getElementById('completedTasks').textContent = taskData.completed;
        document.getElementById('cancelledTasks').textContent = taskData.cancelled;
        document.getElementById('overdueTasks').textContent = taskData.overdue;

        // Create status pie chart
        function createStatusChart() {
            const canvas = document.getElementById('statusChart');
            const ctx = canvas.getContext('2d');
            const centerX = canvas.width / 2;
            const centerY = canvas.height / 2;
            const radius = 100;

            const data = [
                { label: 'Pending', value: taskData.pending, color: '#f39c12' },
                { label: 'In Progress', value: taskData.inProgress, color: '#3498db' },
                { label: 'Completed', value: taskData.completed, color: '#27ae60' },
                { label: 'Cancelled', value: taskData.cancelled, color: '#e74c3c' }
            ];

            let currentAngle = 0;
            const total = data.reduce((sum, item) => sum + item.value, 0);

            data.forEach(item => {
                const sliceAngle = (item.value / total) * 2 * Math.PI;

                ctx.beginPath();
                ctx.moveTo(centerX, centerY);
                ctx.arc(centerX, centerY, radius, currentAngle, currentAngle + sliceAngle);
                ctx.closePath();
                ctx.fillStyle = item.color;
                ctx.fill();

                currentAngle += sliceAngle;
            });

            // Create legend
            const legend = document.getElementById('statusLegend');
            data.forEach(item => {
                const legendItem = document.createElement('div');
                legendItem.className = 'legend-item';
                legendItem.innerHTML = `
                    <div class="legend-color" style="background-color: ${item.color}"></div>
                    <span>${item.label} (${item.value})</span>
                `;
                legend.appendChild(legendItem);
            });
        }

        // Create priority bar chart
        function createPriorityChart() {
            const container = document.getElementById('priorityChart');
            const priorities = [
                { label: 'High', value: taskData.priorities.high, color: '#e74c3c' },
                { label: 'Medium', value: taskData.priorities.medium, color: '#f39c12' },
                { label: 'Low', value: taskData.priorities.low, color: '#27ae60' }
            ];

            const maxValue = Math.max(...priorities.map(p => p.value));

            priorities.forEach(priority => {
                const bar = document.createElement('div');
                bar.className = 'bar';
                bar.style.height = `${(priority.value / maxValue) * 200}px`;
                bar.style.backgroundColor = priority.color;

                const label = document.createElement('div');
                label.className = 'bar-label';
                label.textContent = priority.label;

                const value = document.createElement('div');
                value.className = 'bar-value';
                value.textContent = priority.value;

                bar.appendChild(label);
                bar.appendChild(value);
                container.appendChild(bar);
            });
        }

        // Create task list
        function createTaskList() {
            const taskList = document.getElementById('taskList');

            taskData.recentTasks.forEach(task => {
                const taskItem = document.createElement('div');
                taskItem.className = 'task-item';

                const statusClass = `status-${task.status.replace('_', '-')}`;
                const dueDateText = task.dueDate ? new Date(task.dueDate).toLocaleDateString() : 'No due date';

                taskItem.innerHTML = `
                    <div class="task-title">${task.title}</div>
                    <div class="task-meta">
                        <span class="status-badge ${statusClass}">${task.status.replace('_', ' ')}</span>
                        <span>Priority: ${task.priority}</span> |
                        <span>Due: ${dueDateText}</span>
                    </div>
                `;

                taskList.appendChild(taskItem);
            });
        }

        // Initialize dashboard
        createStatusChart();
        createPriorityChart();
        createTaskList();

        // Add some animations
        window.addEventListener('load', () => {
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';

                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 50);
                }, index * 100);
            });
        });
    </script>
</body>
</html>
