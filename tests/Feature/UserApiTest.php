<?php

namespace Tests\Feature;

use App\Enum\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_list_users()
    {
        $admin = User::factory()->admin()->create();
        User::factory()->count(3)->create();

        $response = $this->actingAs($admin)->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_non_admin_cannot_list_users()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/users');

        $response->assertStatus(401);
    }

    public function test_admin_can_create_user()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->postJson('/api/users', [
            'username' => 'newuser',
            'password' => 'password123',
            'name' => 'New User',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['data']);
    }

    public function test_admin_cannot_create_user_with_invalid_data(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        $response = $this->postJson('/api/users', [
            'username' => 'ab',
            'password' => '12',
            'name' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['username', 'password', 'name']);
    }

    public function test_non_admin_cannot_update_other_user(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($user);

        $response = $this->putJson("/api/users/{$other->id}", ['name' => 'Hacker']);
        $response->assertStatus(401);
    }

    public function test_non_admin_cannot_delete_user(): void
    {
        $user = User::factory()->create();
        $target = User::factory()->create();

        $this->actingAs($user);
        $response = $this->deleteJson("/api/users/{$target->id}");

        $response->assertStatus(401);
    }

    public function test_admin_can_update_user_role(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create(['role' => Role::User]);

        $this->actingAs($admin);
        $response = $this->putJson("/api/users/{$user->id}", ['role' => Role::Admin]);

        $response->assertOk()->assertJsonFragment(['role' => Role::Admin->value]);
    }
}
