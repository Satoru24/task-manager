<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->artisan('migrate:fresh');
    }



    #[Test]
    public function user_can_create_a_task()
    {
        $this->actingAs($this->user);

        $taskData = [
            'title' => 'Complete Laravel project',
            'description' => 'Build a task management application',
            'status' => 'pending',
            'priority' => 'high',
            'due_date' => now()->addDays(7)->format('Y-m-d H:i:s')
        ];

        $response = $this->post('/tasks', $taskData);

        $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'message' => 'Task created successfully'
                ]);

        $this->assertDatabaseHas('tasks', [
            'title' => $taskData['title'],
            'description' => $taskData['description'],
            'user_id' => $this->user->id,
            'status' => 'pending'
        ]);
    }

    #[Test]
    public function user_can_view_all_their_tasks()
    {
        $this->actingAs($this->user);

        // Create multiple tasks for this user
        $tasks = Task::factory()->count(3)->create([
            'user_id' => $this->user->id
        ]);

        // Create tasks for another user (should not be visible)
        $otherUser = User::factory()->create();
        Task::factory()->count(2)->create([
            'user_id' => $otherUser->id
        ]);

        $response = $this->get('/tasks');

        $response->assertStatus(200)
                ->assertJsonCount(3, 'data')
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'title',
                            'description',
                            'status',
                            'priority',
                            'due_date',
                            'created_at',
                            'updated_at'
                        ]
                    ]
                ]);
    }

    #[Test]
    public function user_can_view_a_specific_task()
    {
        $this->actingAs($this->user);

        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Test Task',
            'description' => 'Test Description'
        ]);

        $response = $this->get("/tasks/{$task->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'id' => $task->id,
                        'title' => 'Test Task',
                        'description' => 'Test Description',
                        'user_id' => $this->user->id
                    ]
                ]);
    }

    #[Test]
    public function user_cannot_view_another_users_task()
    {
        $this->actingAs($this->user);

        $otherUser = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $otherUser->id
        ]);

        $response = $this->get("/tasks/{$task->id}");

        $response->assertStatus(404);
    }

    #[Test]
    public function user_can_update_their_task()
    {
        $this->actingAs($this->user);

        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Original Title',
            'status' => 'pending'
        ]);

        $updateData = [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'status' => 'completed',
            'priority' => 'medium'
        ];

        $response = $this->put("/tasks/{$task->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Task updated successfully'
                ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'status' => 'completed',
            'priority' => 'medium'
        ]);
    }

    #[Test]
    public function user_cannot_update_another_users_task()
    {
        $this->actingAs($this->user);

        $otherUser = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $otherUser->id
        ]);

        $updateData = [
            'title' => 'Hacked Title'
        ];

        $response = $this->put("/tasks/{$task->id}", $updateData);

        $response->assertStatus(404);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
            'title' => 'Hacked Title'
        ]);
    }

    #[Test]
    public function user_can_delete_their_task()
    {
        $this->actingAs($this->user);

        $task = Task::factory()->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->delete("/tasks/{$task->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Task deleted successfully'
                ]);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id
        ]);
    }

    #[Test]
    public function user_cannot_delete_another_users_task()
    {
        $this->actingAs($this->user);

        $otherUser = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $otherUser->id
        ]);

        $response = $this->delete("/tasks/{$task->id}");

        $response->assertStatus(404);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id
        ]);
    }

    #[Test]
    public function task_creation_requires_authentication()
    {
        $taskData = [
            'title' => 'Unauthorized Task',
            'description' => 'This should fail'
        ];

        $response = $this->post('/tasks', $taskData);

        $response->assertStatus(401);
    }

    #[Test]
    public function task_creation_validates_required_fields()
    {
        $this->actingAs($this->user);

        $response = $this->post('/tasks', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['title']);
    }

    #[Test]
    public function task_creation_validates_field_lengths()
    {
        $this->actingAs($this->user);

        $taskData = [
            'title' => str_repeat('a', 256), // Assuming max length is 255
            'description' => 'Valid description'
        ];

        $response = $this->post('/tasks', $taskData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['title']);
    }

    #[Test]
    public function task_creation_validates_status_values()
    {
        $this->actingAs($this->user);

        $taskData = [
            'title' => 'Valid Title',
            'status' => 'invalid_status'
        ];

        $response = $this->post('/tasks', $taskData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['status']);
    }

    #[Test]
    public function user_can_filter_tasks_by_status()
    {
        $this->actingAs($this->user);

        Task::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending'
        ]);

        Task::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'completed'
        ]);

        $response = $this->get('/tasks?status=pending');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'data');
    }

    #[Test]
    public function user_can_search_tasks_by_title()
    {
        $this->actingAs($this->user);

        Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Laravel Development'
        ]);

        Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'React Project'
        ]);

        $response = $this->get('/tasks?search=Laravel');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'data');
    }
}
