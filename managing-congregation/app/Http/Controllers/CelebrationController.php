<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Services\CelebrationCardService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CelebrationController extends Controller
{
    public function __construct(
        protected CelebrationCardService $cardService
    ) {}

    public function index()
    {
        // For MVP, just show a list of upcoming celebrations
        $upcomingBirthdays = Member::whereRaw('DATE_ADD(dob, INTERVAL YEAR(CURDATE())-YEAR(dob) + IF(DAYOFYEAR(CURDATE()) > DAYOFYEAR(dob),1,0) YEAR) BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)')
            ->get();
            
        return view('celebrations.index', compact('upcomingBirthdays'));
    }

    public function generateBirthday(Member $member)
    {
        $image = $this->cardService->generateBirthdayCard($member);

        return response($image)
            ->header('Content-Type', 'image/png');
    }

    public function downloadBirthday(Member $member)
    {
        $image = $this->cardService->generateBirthdayCard($member);

        return response($image)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="birthday-card-' . $member->id . '.png"');
    }
}
