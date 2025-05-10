<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoteApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_note()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/notes', [
            'title' => 'Test Note',
            'content' => 'This is the content.',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['data']);
    }

    public function test_user_can_list_own_and_public_notes()
    {
        $user = User::factory()->create();
        Note::factory()->create(['user_id' => $user->id]);
        Note::factory()->create(['is_public' => true]);

        $response = $this->actingAs($user)->getJson('/api/notes');

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_user_can_view_own_note()
    {
        $user = User::factory()->create();
        $note = Note::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson("/api/notes/{$note->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_user_cannot_view_private_note_of_other_user()
    {
        $user = User::factory()->create();
        $note = Note::factory()->create(['is_public' => false]);

        $response = $this->actingAs($user)->getJson("/api/notes/{$note->id}");

        $response->assertStatus(401);
    }

    public function test_note_creation_requires_fields(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/notes', []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'content']);
    }

    public function test_non_owner_cannot_update_note(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $note = Note::factory()->for($owner)->create();

        $this->actingAs($other);

        $response = $this->putJson("/api/notes/{$note->id}", ['title' => 'Hack']);
        $response->assertStatus(401);
    }

    public function test_user_cannot_delete_others_note(): void
    {
        $owner = User::factory()->create();
        $attacker = User::factory()->create();
        $note = Note::factory()->for($owner)->create();

        $this->actingAs($attacker);
        $response = $this->deleteJson("/api/notes/{$note->id}");

        $response->assertStatus(401);
    }

    public function test_note_search_by_title_or_content(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Note::factory()->for($user)->create(['title' => 'Meeting notes', 'content' => 'Budget Q2']);
        Note::factory()->for($user)->create(['title' => 'Shopping list', 'content' => 'Eggs']);

        $response = $this->getJson('/api/notes?search=Budget');
        $response->assertOk()->assertJsonFragment(['title' => 'Meeting notes']);
    }

    public function test_cannot_update_note_with_invalid_data(): void
    {
        $user = User::factory()->create();
        $note = Note::factory()->for($user)->create();

        $this->actingAs($user);
        $response = $this->putJson("/api/notes/{$note->id}", ['is_public' => 'notaboolean']);

        $response->assertStatus(422)->assertJsonValidationErrors(['is_public']);
    }
}
