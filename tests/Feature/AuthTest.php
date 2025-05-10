<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register()
    {
        $response = $this->postJson('/api/register', [
            'username' => 'john_doe',
            'password' => 'password123',
            'name' => 'John Doe',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token', 'user']);
    }

    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'username' => 'jane',
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->postJson('/api/login', [
            'username' => 'jane',
            'password' => 'secret123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token', 'user']);
    }

    public function test_authenticated_user_can_get_info()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/info');

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_registration_requires_fields(): void
    {
        $response = $this->postJson('/api/register', []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['username', 'password', 'name']);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $user = User::factory()->create(['password' => bcrypt('secret123')]);

        $response = $this->postJson('/api/login', [
            'username' => $user->username,
            'password' => 'wrongpass',
        ]);

        $response->assertStatus(401);
    }

    public function test_login_requires_username_and_password(): void
    {
        $response = $this->postJson('/api/login', []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['username', 'password']);
    }

    public function test_register_fails_with_duplicate_username(): void
    {
        User::factory()->create(['username' => 'johndoe']);

        $response = $this->postJson('/api/register', [
            'username' => 'johndoe',
            'password' => 'password123',
            'name' => 'John',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['username']);
    }
}
