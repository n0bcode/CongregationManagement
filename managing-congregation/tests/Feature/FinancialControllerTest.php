<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Community;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionSeeder::class);
    }

    public function test_director_can_view_financials()
    {
        $community = Community::factory()->create();
        $director = User::factory()->create(['role' => UserRole::DIRECTOR, 'community_id' => $community->id]);

        $response = $this->actingAs($director)->get(route('financials.index'));

        $response->assertStatus(200);
        $response->assertViewIs('financials.index');
    }

    public function test_director_can_create_expense()
    {
        $community = Community::factory()->create();
        $director = User::factory()->create(['role' => UserRole::DIRECTOR, 'community_id' => $community->id]);

        $response = $this->actingAs($director)->post(route('financials.store'), [
            'community_id' => $community->id,
            'description' => 'Test Expense',
            'amount' => 100.00,
            'date' => now()->format('Y-m-d'),
            'category' => 'supplies',
        ]);

        $response->assertRedirect(route('financials.index'));
        $this->assertDatabaseHas('expenses', [
            'description' => 'Test Expense',
            'amount' => 10000, // stored in cents
            'community_id' => $community->id,
        ]);
    }

    public function test_director_cannot_view_other_community_financials()
    {
        $community1 = Community::factory()->create();
        $director = User::factory()->create(['role' => UserRole::DIRECTOR, 'community_id' => $community1->id]);

        $community2 = Community::factory()->create();
        $expense = Expense::factory()->create(['community_id' => $community2->id]);

        $response = $this->actingAs($director)->get(route('financials.show', $expense));

        $response->assertStatus(404);
    }

    public function test_general_treasurer_can_view_all_financials()
    {
        $general = User::factory()->create(['role' => UserRole::GENERAL]);
        $community = Community::factory()->create();
        $expense = Expense::factory()->create(['community_id' => $community->id]);

        $response = $this->actingAs($general)->get(route('financials.index'));
        $response->assertStatus(200);
    }

    public function test_general_treasurer_cannot_create_expense()
    {
        $general = User::factory()->create(['role' => UserRole::GENERAL]);
        $community = Community::factory()->create();

        $response = $this->actingAs($general)->post(route('financials.store'), [
            'community_id' => $community->id,
            'description' => 'Unauthorized Expense',
            'amount' => 100.00,
            'date' => now()->format('Y-m-d'),
            'category' => 'supplies',
        ]);

        $response->assertStatus(403);
    }
}
