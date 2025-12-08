<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValidationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_validate_field_successfully()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson(route('api.validate'), [
                'field' => 'email',
                'value' => 'test@example.com',
                'rules' => 'required|email',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'valid' => true,
            ]);
    }

    public function test_returns_error_for_invalid_field()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson(route('api.validate'), [
                'field' => 'email',
                'value' => 'invalid-email',
                'rules' => 'required|email',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'valid' => false,
                'message' => 'The email field must be a valid email address.',
            ]);
    }

    public function test_validates_unique_rule()
    {
        $user = User::factory()->create(['email' => 'existing@example.com']);
        $otherUser = User::factory()->create();

        $response = $this->actingAs($otherUser)
            ->postJson(route('api.validate'), [
                'field' => 'email',
                'value' => 'existing@example.com',
                'rules' => 'required|email|unique:users,email',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'valid' => false,
                'message' => 'The email has already been taken.',
            ]);
    }

    public function test_validates_unique_rule_ignoring_current_id()
    {
        $user = User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->actingAs($user)
            ->postJson(route('api.validate'), [
                'field' => 'email',
                'value' => 'existing@example.com',
                'rules' => 'required|email|unique:users,email',
                'id' => $user->id,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'valid' => true,
            ]);
    }
}
