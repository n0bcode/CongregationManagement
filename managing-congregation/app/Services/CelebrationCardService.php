<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Member;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class CelebrationCardService
{
    protected ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver);
    }

    public function generateBirthdayCard(Member $member): string
    {
        return $this->generateCard($member, 'Happy Birthday!', 'May God bless you abundantly on your special day.');
    }

    public function generateVowAnniversaryCard(Member $member, int $years): string
    {
        return $this->generateCard($member, 'Happy Anniversary!', "Celebrating {$years} years of consecrated life.");
    }

    public function generateOrdinationCard(Member $member, int $years): string
    {
        return $this->generateCard($member, 'Ordination Anniversary', "Celebrating {$years} years of priesthood.");
    }

    protected function generateCard(Member $member, string $title, string $message): string
    {
        // Create a blank canvas
        $image = $this->manager->create(800, 600)->fill('#f7fafc');

        // Add border
        $image->drawRectangle(20, 20, function ($draw) {
            $draw->size(760, 560);
            $draw->border('#d4af37', 5); // Sanctuary Gold
        });

        // Add Member Name
        $image->text($member->first_name.' '.$member->last_name, 400, 200, function ($font) {
            $font->file(public_path('fonts/Roboto-Regular.ttf'));
            $font->size(48);
            $font->color('#1a202c');
            $font->align('center');
            $font->valign('middle');
        });

        // Add Title
        $image->text($title, 400, 300, function ($font) {
            $font->size(36);
            $font->color('#d4af37');
            $font->align('center');
            $font->valign('middle');
        });

        // Add Message
        $image->text($message, 400, 400, function ($font) {
            $font->size(24);
            $font->color('#4a5568');
            $font->align('center');
            $font->valign('middle');
        });

        // Add Logo (placeholder if not exists)
        // $image->place(public_path('images/logo.png'), 'bottom-right', 30, 30);

        return (string) $image->toPng();
    }
}
