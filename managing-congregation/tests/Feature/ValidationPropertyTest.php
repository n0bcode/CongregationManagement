<?php

namespace Tests\Feature;

use App\Enums\FormationStage;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ValidationPropertyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Property 43: Date of Birth Cannot Be Future
     * Validates: Requirements 14.1
     */
    public function test_dob_cannot_be_future()
    {
        $community = \App\Models\Community::factory()->create();
        $user = User::factory()->create([
            'role' => \App\Enums\UserRole::MEMBER,
            'community_id' => $community->id,
        ]);

        // Grant permission
        $permission = \App\Models\Permission::create(['key' => 'members.create', 'name' => 'Create Members', 'module' => 'members']);
        \Illuminate\Support\Facades\DB::table('role_permissions')->insert([
            'role' => $user->role->value,
            'permission_id' => $permission->id,
        ]);

        $response = $this->actingAs($user)->post(route('members.store'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'dob' => now()->addDay()->format('Y-m-d'),
            'entry_date' => now()->format('Y-m-d'),
        ]);

        $response->assertSessionHasErrors('dob');

        $response = $this->actingAs($user)->post(route('members.store'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'dob' => now()->subDay()->format('Y-m-d'),
            'entry_date' => now()->format('Y-m-d'),
        ]);

        $response->assertSessionHasNoErrors('dob');
    }

    /**
     * Property 44: Formation Dates Are Chronological
     * Validates: Requirements 14.2
     */
    public function test_formation_dates_chronology()
    {
        $user = User::factory()->director()->create();
        $member = Member::factory()->create(['entry_date' => '2020-01-01', 'community_id' => $user->community_id]);

        // Grant permissions
        $permission = \App\Models\Permission::create(['key' => 'members.view', 'name' => 'View Members', 'module' => 'members']);
        \Illuminate\Support\Facades\DB::table('role_permissions')->insert([
            'role' => $user->role->value,
            'permission_id' => $permission->id,
        ]);

        // 1. Postulancy after entry
        $response = $this->actingAs($user)->post(route('members.formation.store', $member), [
            'stage' => FormationStage::Postulancy->value,
            'started_at' => '2019-12-31', // Before entry
        ]);
        $response->assertSessionHasErrors('started_at');

        $response = $this->actingAs($user)->post(route('members.formation.store', $member), [
            'stage' => FormationStage::Postulancy->value,
            'started_at' => '2020-02-01', // After entry
        ]);
        $response->assertSessionHasNoErrors();

        // Create the event manually to proceed
        $member->formationEvents()->create([
            'stage' => FormationStage::Postulancy,
            'started_at' => '2020-02-01',
        ]);

        // 2. Novitiate after Postulancy
        $response = $this->actingAs($user)->post(route('members.formation.store', $member), [
            'stage' => FormationStage::Novitiate->value,
            'started_at' => '2020-01-15', // Before Postulancy
        ]);
        $response->assertSessionHasErrors('started_at');

        $response = $this->actingAs($user)->post(route('members.formation.store', $member), [
            'stage' => FormationStage::Novitiate->value,
            'started_at' => '2020-03-01', // After Postulancy
        ]);
        $response->assertSessionHasNoErrors();
    }

    /**
     * Property 45: Assignments Cannot Overlap
     * Validates: Requirements 14.3
     */
    public function test_assignments_cannot_overlap()
    {
        $user = User::factory()->director()->create();
        $member = Member::factory()->create(['community_id' => $user->community_id]);

        // Create initial assignment
        $member->assignments()->create([
            'community_id' => $user->community_id,
            'start_date' => '2023-01-01',
            'end_date' => '2023-06-30',
        ]);

        // Grant permission
        $permission = \App\Models\Permission::create(['key' => 'members.edit', 'name' => 'Edit Members', 'module' => 'members']);
        \Illuminate\Support\Facades\DB::table('role_permissions')->insert([
            'role' => $user->role->value,
            'permission_id' => $permission->id,
        ]);

        // 1. Overlap start
        $response = $this->actingAs($user)->post(route('members.assignments.store', $member), [
            'community_id' => $user->community_id,
            'start_date' => '2023-06-01',
            'end_date' => '2023-12-31',
        ]);
        $response->assertSessionHasErrors('start_date');

        // 2. Overlap end
        $response = $this->actingAs($user)->post(route('members.assignments.store', $member), [
            'community_id' => $user->community_id,
            'start_date' => '2022-06-01',
            'end_date' => '2023-01-15',
        ]);
        $response->assertSessionHasErrors('start_date');

        // 3. Complete overlap (inside)
        $response = $this->actingAs($user)->post(route('members.assignments.store', $member), [
            'community_id' => $user->community_id,
            'start_date' => '2023-02-01',
            'end_date' => '2023-05-01',
        ]);
        $response->assertSessionHasErrors('start_date');

        // 4. No overlap (after)
        $response = $this->actingAs($user)->post(route('members.assignments.store', $member), [
            'community_id' => $user->community_id,
            'start_date' => '2023-07-01',
            'end_date' => '2023-12-31',
        ]);
        $response->assertSessionHasNoErrors();
    }

    /**
     * Property 47: File Uploads Are Validated
     * Validates: Requirements 14.5
     */
    public function test_file_uploads_are_validated()
    {
        Storage::fake('private');
        Storage::fake('public');

        $user = User::factory()->director()->create();
        $member = Member::factory()->create(['community_id' => $user->community_id]);

        // Grant permissions
        $permUpload = \App\Models\Permission::create(['key' => 'documents.upload', 'name' => 'Upload Documents', 'module' => 'documents']);
        $permEdit = \App\Models\Permission::create(['key' => 'members.edit', 'name' => 'Edit Members', 'module' => 'members']);

        \Illuminate\Support\Facades\DB::table('role_permissions')->insert([
            ['role' => $user->role->value, 'permission_id' => $permUpload->id],
            ['role' => $user->role->value, 'permission_id' => $permEdit->id],
        ]);

        // 1. Document Type Validation
        $response = $this->actingAs($user)->post(route('documents.store'), [
            'title' => 'Test Doc',
            'category' => \App\Enums\DocumentCategory::OTHER->value,
            'file' => UploadedFile::fake()->create('test.txt', 100), // Invalid type
        ]);
        $response->assertSessionHasErrors('file');

        $response = $this->actingAs($user)->post(route('documents.store'), [
            'title' => 'Test Doc',
            'category' => \App\Enums\DocumentCategory::OTHER->value,
            'file' => UploadedFile::fake()->create('test.pdf', 100), // Valid type
        ]);
        $response->assertSessionHasNoErrors();

        // 2. Photo Size Validation
        $response = $this->actingAs($user)->put(route('members.photo.update', $member), [
            'photo' => UploadedFile::fake()->image('large.jpg')->size(6000), // 6MB > 5MB
        ]);
        $response->assertSessionHasErrors('photo');

        $response = $this->actingAs($user)->put(route('members.photo.update', $member), [
            'photo' => UploadedFile::fake()->image('valid.jpg')->size(4000), // 4MB < 5MB
        ]);
        $response->assertSessionHasNoErrors();
    }
}
