<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MemberPhotoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionSeeder::class);
    }

    public function test_can_upload_profile_photo()
    {
        Storage::fake('public');

        $community = \App\Models\Community::factory()->create();
        $user = User::factory()->create(['role' => \App\Enums\UserRole::DIRECTOR, 'community_id' => $community->id]);
        $member = Member::factory()->create(['community_id' => $community->id]);

        $file = UploadedFile::fake()->image('photo.jpg');

        $response = $this->actingAs($user)
            ->put(route('members.photo.update', $member), [
                'photo' => $file,
            ]);

        $response->assertSessionHas('success');

        $member->refresh();
        $this->assertNotNull($member->profile_photo_path);
        Storage::disk('public')->assertExists($member->profile_photo_path);
    }

    public function test_can_delete_profile_photo()
    {
        Storage::fake('public');

        $community = \App\Models\Community::factory()->create();
        $user = User::factory()->create(['role' => \App\Enums\UserRole::DIRECTOR, 'community_id' => $community->id]);
        $member = Member::factory()->create([
            'community_id' => $community->id,
            'profile_photo_path' => 'profile-photos/old-photo.jpg',
        ]);

        // Create the fake file
        Storage::disk('public')->put('profile-photos/old-photo.jpg', 'content');

        $response = $this->actingAs($user)
            ->delete(route('members.photo.destroy', $member));

        $response->assertSessionHas('success');

        $member->refresh();
        $this->assertNull($member->profile_photo_path);
        Storage::disk('public')->assertMissing('profile-photos/old-photo.jpg');
    }

    public function test_photo_validation()
    {
        $community = \App\Models\Community::factory()->create();
        $user = User::factory()->create(['role' => \App\Enums\UserRole::DIRECTOR, 'community_id' => $community->id]);
        $member = Member::factory()->create(['community_id' => $community->id]);

        $file = UploadedFile::fake()->create('document.pdf', 100); // Not an image

        $response = $this->actingAs($user)
            ->put(route('members.photo.update', $member), [
                'photo' => $file,
            ]);

        $response->assertSessionHasErrors(['photo']);
    }
}
