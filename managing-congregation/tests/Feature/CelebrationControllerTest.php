<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\User;
use App\Services\CelebrationCardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CelebrationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_page_loads()
    {
        $user = User::factory()->create(['role' => \App\Enums\UserRole::SUPER_ADMIN]);

        $response = $this->actingAs($user)->get(route('celebrations.index'));

        $response->assertStatus(200);
        $response->assertViewIs('celebrations.index');
    }

    public function test_generate_birthday_returns_image()
    {
        $user = User::factory()->create(['role' => \App\Enums\UserRole::SUPER_ADMIN]);
        $member = Member::factory()->create();

        $this->mock(CelebrationCardService::class, function ($mock) {
            $mock->shouldReceive('generateBirthdayCard')
                ->once()
                ->andReturn('fake-image-data');
        });

        $response = $this->actingAs($user)->get(route('celebrations.birthday.generate', $member));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/png');
        $this->assertEquals('fake-image-data', $response->getContent());
    }

    public function test_download_birthday_downloads_image()
    {
        $user = User::factory()->create(['role' => \App\Enums\UserRole::SUPER_ADMIN]);
        $member = Member::factory()->create();

        $this->mock(CelebrationCardService::class, function ($mock) {
            $mock->shouldReceive('generateBirthdayCard')
                ->once()
                ->andReturn('fake-image-data');
        });

        $response = $this->actingAs($user)->get(route('celebrations.birthday.download', $member));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/png');
        $response->assertHeader('Content-Disposition', 'attachment; filename="birthday-card-'.$member->id.'.png"');
    }

    public function test_email_birthday_sends_email()
    {
        Mail::fake();
        $user = User::factory()->create(['role' => \App\Enums\UserRole::SUPER_ADMIN]);
        $member = Member::factory()->create(['email' => 'test@example.com']);

        $this->mock(CelebrationCardService::class, function ($mock) {
            $mock->shouldReceive('generateBirthdayCard')
                ->once()
                ->andReturn('fake-image-data');
        });

        $response = $this->actingAs($user)->post(route('celebrations.birthday.email', $member));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        Mail::assertSent(\App\Mail\CelebrationCardMail::class, function ($mail) use ($member) {
            return $mail->hasTo($member->email);
        });
    }

    public function test_email_birthday_fails_if_no_email()
    {
        Mail::fake();
        $user = User::factory()->create(['role' => \App\Enums\UserRole::SUPER_ADMIN]);
        $member = Member::factory()->create(['email' => null]);

        $response = $this->actingAs($user)->post(route('celebrations.birthday.email', $member));

        $response->assertRedirect();
        $response->assertSessionHas('error');

        Mail::assertNothingSent();
    }
}
