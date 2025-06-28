<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Category;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Sanctum::actingAs(User::factory()->create(), ['Auth-Token']);
    }

    #[Test]
    public function user_can_create_category(): void
    {
        $response = $this->postJson('/api/categories', [
            'name' => 'Test Category',
        ]);

        $response->assertStatus(201)
                 ->assertJson(['name' => 'Test Category']);

        $this->assertDatabaseHas('categories', ['name' => 'Test Category']);
    }

    #[Test]
    public function user_can_view_all_categories(): void
    {
        Category::factory()->count(3)->create();

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    #[Test]
    public function user_can_view_single_category(): void
    {
        $category = Category::factory()->create();

        $response = $this->getJson('/api/categories/' . $category->id);

        $response->assertStatus(200)
                 ->assertJson(['id' => $category->id, 'name' => $category->name]);
    }

    #[Test]
    public function user_can_update_category(): void
    {
        $category = Category::factory()->create();

        $response = $this->putJson('/api/categories/' . $category->id, [
            'name' => 'Updated Category',
        ]);

        $response->assertStatus(200)
                 ->assertJson(['name' => 'Updated Category']);

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Updated Category']);
    }

    #[Test]
    public function user_can_delete_category(): void
    {
        $category = Category::factory()->create();

        $response = $this->deleteJson('/api/categories/' . $category->id);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Category deleted successfully']);

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
