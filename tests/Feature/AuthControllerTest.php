<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_register(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'token_type',
                     'token',
                 ]);

        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    #[Test]
    public function user_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password')
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'token_type',
                     'token'
                 ]);
    }

    #[Test]
    public function authenticated_user_can_access_profile(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['Auth-Token']);

        $response = $this->getJson('/api/profile');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'data' => ['id', 'name', 'email']
                 ]);
    }

    #[Test]
    public function user_can_logout(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['Auth-Token']);

        $response = $this->postJson('/api/logout');

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Logged out successfully' 
                 ]);
    }
}
