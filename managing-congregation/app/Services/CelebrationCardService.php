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

    public function generateBirthdayCard(Member $member, string $title = 'Happy Birthday!', string $message = 'Wishing you a year filled with blessings!', string $font = 'Caveat-VariableFont_wght.ttf'): string
    {
        return $this->generateCard($member, $title, $message, $font);
    }

    public function generateVowAnniversaryCard(Member $member, int $years): string
    {
        return $this->generateCard($member, 'Happy Anniversary!', "Celebrating {$years} years of consecrated life.");
    }

    public function generateOrdinationCard(Member $member, int $years): string
    {
        // For ordination we might want to allow font choice too, but let's stick to birthday request first.
        return $this->generateCard($member, 'Ordination Anniversary', "Celebrating {$years} years of priesthood.");
    }

    protected function generateCard(Member $member, string $title, string $message, string $font = 'Caveat-VariableFont_wght.ttf'): string
    {
        // Validate font to prevent directory traversal
        $allowedFonts = ['Caveat-VariableFont_wght.ttf', 'FleurDeLeah-Regular.ttf', 'Roboto-Regular.ttf'];
        if (!in_array($font, $allowedFonts)) {
            $font = 'Caveat-VariableFont_wght.ttf';
        }
        $fontPath = public_path("fonts/{$font}");

        // Create canvas with a soft gradient background (simulated by filling with a base color)
        // For a more advanced gradient, we'd need loop drawing, but let's start with a nice off-white/cream
        // or a very light pastel color.
        $width = 800;
        $height = 600;
        $image = $this->manager->create($width, $height)->fill('#fffcf5'); // Creamy white

        // Add "Confetti" - randomized colorful circles
        $colors = ['#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEEAD', '#D4AF37', '#FF9F1C'];
        
        for ($i = 0; $i < 50; $i++) {
            $x = rand(0, $width);
            $y = rand(0, $height);
            $size = rand(5, 15);
            $color = $colors[array_rand($colors)];
            
            $image->drawCircle($x, $y, function ($draw) use ($size, $color) {
                $draw->radius($size);
                $draw->background($color); // Used 'background' for fill color
            }); 
        }

        // Add specific decorative border (Double border)
        $image->drawRectangle(20, 20, function ($draw) {
            $draw->size(760, 560);
            $draw->border('#d4af37', 8); // Thick Gold
        });
        
        $image->drawRectangle(35, 35, function ($draw) {
            $draw->size(730, 530);
            $draw->border('#d4af37', 2); // Thin Gold Inner
        });

        // Add Member Name (Big & Bold)
        $image->text($member->first_name.' '.$member->last_name, 400, 240, function ($fontObj) use ($fontPath) {
            $fontObj->file($fontPath);
            $fontObj->size(80); // Larger
            $fontObj->color('#000918ff'); // Dark Slate
            $fontObj->align('center');
            $fontObj->valign('middle');
        });

        // Add Title ("Happy Birthday")
        // Shadow Layer
        $image->text($title, 402, 352, function ($fontObj) use ($fontPath) { // Offset by 3px
            $fontObj->file($fontPath);
            $fontObj->size(42);
            $fontObj->color('rgba(248, 255, 48, 0.2)'); // Soft shadow
            $fontObj->align('center');
            $fontObj->valign('middle');
        });

        // Main Layer
        $image->text($title, 400, 350, function ($fontObj) use ($fontPath) {
            $fontObj->file($fontPath);
            $fontObj->size(42);
            $fontObj->color('#8c1700ff'); // Gold
            $fontObj->align('center');
            $fontObj->valign('middle');
        });

        // Add Message
        $image->text($message, 400, 450, function ($fontObj) use ($fontPath) {
            $fontObj->file($fontPath);
            $fontObj->size(28);
            $fontObj->color('#001e51ff');
            $fontObj->align('center');
            $fontObj->valign('middle');
        });
        
        // Add footer branding
        $branding = $member->community ? $member->community->name : config('app.name');
        
        $image->text($branding, 400, 550, function ($fontObj) use ($fontPath) {
            $fontObj->file($fontPath);
            $fontObj->size(28); // Increased size for branding visibility with this font
            $fontObj->color('#718096'); // Gray 600
            $fontObj->align('center');
            $fontObj->valign('middle');
        });

        // Add Graphics
        // Cross Top Left (Watermark style)
        if (file_exists(public_path('images/cross.png'))) {
            $image->place(public_path('images/cross.png'), 'top-left', 30, 30, 30); // 30% opacity
        }

        // Cake Bottom Right
        if (file_exists(public_path('images/cake.png'))) {
            // Resize cake if needed or just place it. Assuming the icon is roughly icon sized.
            // If the generated image is large (1024x1024), we should resize it first.
            // Since we can't resize easily without loading it separately, let's try to load and resize.
            $cakePath = public_path('images/cake.png');
            try {
               $cake = $this->manager->read($cakePath);
               $cake->resize(120, 120); // Resize to 120px
               $image->place($cake, 'bottom-right', 40, 40);
            } catch (\Exception $e) {
               // Ignore if fails
            }
        }
        
        // Also place a second cross on top right for symmetry? No, simpler is better.
        // Maybe place the cross centered at the very top?
        if (file_exists(public_path('images/cross.png'))) {
             try {
               $cross = $this->manager->read(public_path('images/cross.png'));
               $cross->resize(60, 60);
               $image->place($cross, 'top-center', 0, 40);
            } catch (\Exception $e) {
               // Ignore
            }
        }

        return (string) $image->toPng();
    }
}
