<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use WithFaker;

    public function test_can_create_task_by_user(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $user_id = $user->id;
        $title = $this->faker->sentence;
        $description = $this->faker->paragraph;
        $status = 'pending';
        $due_date = Carbon::now()->addRealDays(3)->toDateTimeString();

        $response = $this->actingAs($user, 'api')
            ->postJson('/api/tasks/', [
                'user_id' => $user_id,
                'title' => $title,
                'description' => $description,
                'status' => $status,
                'due_date' => $due_date,

            ]);

        $response
            ->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'task created successfully',
                'result' => [
                    'title' => $title,
                    'description' => $description,
                    'status' => $status,
                    'due_date' => $due_date,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                    ],
                ],
            ]);

        $this->assertDatabaseHas('tasks', [
            'user_id' => $user_id,
            'title' => $title,
            'description' => $description,
            'status' => $status,
            'due_date' => $due_date,
        ]);
    }

    public function test_can_create_task_by_admin(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $user_id = $user->id;
        $title = $this->faker->sentence;
        $description = $this->faker->paragraph;
        $status = 'pending';
        $due_date = Carbon::now()->addRealDays(3)->toDateTimeString();

        $response = $this->actingAs($admin, 'api')
            ->postJson('/api/tasks/', [
                'user_id' => $user_id,
                'title' => $title,
                'description' => $description,
                'status' => $status,
                'due_date' => $due_date,
            ]);

        $response
            ->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'task created successfully',
                'result' => [
                    'title' => $title,
                    'description' => $description,
                    'status' => $status,
                    'due_date' => $due_date,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                    ]
                ],
            ]);

        $this->assertDatabaseHas('tasks', [
            'user_id' => $user_id,
            'title' => $title,
            'description' => $description,
            'status' => $status,
            'due_date' => $due_date,
        ]);
    }

    public function test_user_cannot_create_task_for_another_user(): void
    {
        $user1 = User::factory()->create([
            'role' => 'user',
        ]);

        $user2 = User::factory()->create([
            'role' => 'user',
        ]);

        $user_id = $user1->id;
        $title = $this->faker->sentence;
        $description = $this->faker->paragraph;
        $status = 'pending';
        $due_date = Carbon::now()->addRealDays(3)->toDateTimeString();

        $response = $this->actingAs($user2, 'api')
            ->postJson('/api/tasks/', [
                'user_id' => $user_id,
                'title' => $title,
                'description' => $description,
                'status' => $status,
                'due_date' => $due_date,
            ]);

        $response
            ->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'an error occurred',
                'result' => null,
                'exception' => [
                    'error' => 'Unauthorized',
                ],
            ]);
    }

    public function test_can_fetch_task_by_id(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $task = Task::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user, 'api');

        $response = $this->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200);

        $response->assertJson([
            'success' => true,
            'message' => 'fetched task successfully',
            'result' => [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'status' => $task->status,
                'due_date' => $task->due_date,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ]
            ],
        ]);
    }

    public function test_cannot_fetch_task_with_invalid_id(): void
    {
        $user = User::factory()->create([
            'role' => 'user'
        ]);

        $response = $this->actingAs($user, 'api');

        $response = $this->getJson('/api/tasks/1234531');

        $response->assertStatus(404);

        $response->assertJson([
            'success' => false,
            'message' => 'an error occurred',
            'result' => null,
            'exception' => [
                'error' => 'not found'
            ]
        ]);
    }

    public function test_can_fetch_tasks_by_user(): void
    {
        $user = User::factory()->create([
            'role' => 'user'
        ]);

        Task::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);
        $response = $this->actingAs($user, 'api');
        $response = $this->getJson('api/tasks/');
        $response->assertStatus(200);
    }

    public function test_can_update_task(): void
    {
        $user = User::factory()->create([
            'role' => 'user'
        ]);

        $response = $this->actingAs($user, 'api');

        $task = Task::factory()->create([
            'user_id' => $user->id,
        ]);

        $title = $this->faker->sentence;
        $description = $this->faker->paragraph;
        $status = 'in-progress';
        $due_date = Carbon::now()->addRealDays(3)->toDateTimeString();

        $response = $this->putJson("/api/tasks/{$task->id}", [
            'user_id' => $user->id,
            'title' => $title,
            'description' => $description,
            'status' => $status,
            'due_date' => $due_date,
        ]);

        $response->assertStatus(200);

        $response->assertJson([
            'success' => true,
            'message' => 'updating task successfully',
            'result' => null,
        ]);

        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'title' => $title,
            'description' => $description,
            'status' => $status,
            'due_date' => $due_date,
        ]);
    }

    public function test_cannot_update_task_with_invalid_id(): void
    {
        $user = User::factory()->create([
            'role' => 'user'
        ]);

        $response = $this->actingAs($user, 'api');

        $title = $this->faker->sentence;
        $description = $this->faker->paragraph;
        $status = 'in-progress';
        $due_date = Carbon::now()->addRealDays(3)->toDateTimeString();

        $response = $this->putJson("/api/tasks/3219856", [
            'user_id' => $user->id,
            'title' => $title,
            'description' => $description,
            'status' => $status,
            'due_date' => $due_date,
        ]);

        $response->assertStatus(404);

        $response->assertJson([
            'success' => false,
            'message' => 'an error occurred',
            'result' => null,
            'exception' => [
                'error' => 'not found',
            ],
        ]);
    }

    public function test_can_delete_task(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $response = $this->actingAs($user, 'api');

        $task = Task::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200);

        $response->assertJson([
            'success' => true,
            'message' => 'deleting task successfully',
        ]);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    public function test_cannot_delete_task_with_invalid_id(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $response = $this->actingAs($user, 'api');

        $response = $this->deleteJson('/api/tasks/4712345');

        $response->assertStatus(404);

        $response->assertJson([
            'success' => false,
            'message' => 'an error occurred',
            'result' => null,
            'exception' => [
                'error' => 'not found',
            ],
        ]);
    }
}
