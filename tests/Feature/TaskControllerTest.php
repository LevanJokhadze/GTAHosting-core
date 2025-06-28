<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Task;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;

class TaskControllerTest extends TestCase
{

    protected User $user;
   

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, ['Auth-Token']);
    }

    #[Test]
    public function user_can_create_task(): void
    {
        $category = Category::factory()->create();

        $response = $this->postJson('/api/tasks', [
            'title' => 'New Task',
            'description' => 'Task description',
            'due_date' => now()->addDays(3)->toDateString(),
            'priority' => 'high',
            'category_id' => $category->id,
            'status' => 'pending', 
            'user_id' => $this->user->id,  
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['message', 'data' => ['id', 'title']]);

        $this->assertDatabaseHas('tasks', ['title' => 'New Task']);
    }

    #[Test]
    public function user_can_list_tasks_with_filters(): void
    {
        $category = Category::factory()->create();

        Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Filter Test',
            'priority' => 'low',
            'status' => 'pending',
            'due_date' => now()->toDateString(),
            'category_id' => $category->id,
        ]);

        $response = $this->getJson('/api/tasks?status=pending&priority=low&due_date=' . now()->toDateString());

        $response->assertStatus(200)
                 ->assertJsonFragment(['title' => 'Filter Test']);
    }
    #[Test]
public function user_can_see_profile_with_tasks(): void
{
    $user = User::factory()->create(); 
    $task = Task::factory()->create(['user_id' => $user->id]);

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/profile');
    $response->dump();

    $response->assertStatus(200)
    ->assertJsonStructure([
        'message',
        'data' => ['id', 'name', 'email'],
        'tasks' => [
            ['id', 'user_id', 'title', 'description', 'due_date', 'priority', 'status', 'created_at','updated_at', 'category_id']
        ]
    ])
    ->assertJsonFragment(['name' => $user->name])
    ->assertJsonCount(1, 'tasks');

}

    #[Test]     
    public function user_can_delete_own_task(): void
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson('/api/tasks/' . $task->id);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Task deleted successfully']);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}
