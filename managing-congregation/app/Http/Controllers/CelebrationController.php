<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Services\CelebrationCardService;
use Illuminate\Http\Request;

class CelebrationController extends Controller
{
    public function __construct(
        protected CelebrationCardService $cardService
    ) {}

    public function index()
    {
        // Upcoming Birthdays
        $upcomingBirthdays = Member::upcomingBirthdays()->get();

        // Upcoming Vow Anniversaries (assuming first_vows_date exists, otherwise need to check schema)
        // For MVP, let's stick to birthdays first as schema might not have vow dates easily queryable without joining formation_events
        // Actually, let's check formation events for 'first_vows' or 'perpetual_vows'

        return view('celebrations.index', compact('upcomingBirthdays'));
    }

    public function generateBirthday(Request $request, Member $member)
    {
        $font = $request->input('font', 'Caveat-VariableFont_wght.ttf');
        $image = $this->cardService->generateBirthdayCard($member, 'Happy Birthday!', 'Wishing you a year filled with blessings!', $font);

        return response($image)
            ->header('Content-Type', 'image/png');
    }

    public function downloadBirthday(Request $request, Member $member)
    {
        $font = $request->input('font', 'Caveat-VariableFont_wght.ttf');
        $image = $this->cardService->generateBirthdayCard($member, 'Happy Birthday!', 'Wishing you a year filled with blessings!', $font);

        return response($image)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="birthday-card-'.$member->id.'.png"');
    }

    public function emailBirthday(Request $request, Member $member)
    {
        if (! $member->email) {
            return back()->with('error', 'Member does not have an email address.');
        }

        $font = $request->input('font', 'Caveat-VariableFont_wght.ttf');
        $image = $this->cardService->generateBirthdayCard($member, 'Happy Birthday!', 'Wishing you a year filled with blessings!', $font);

        \Illuminate\Support\Facades\Mail::to($member->email)->send(new \App\Mail\CelebrationCardMail($member, $image, 'Happy Birthday!'));

        return back()->with('success', 'Birthday card sent successfully!');
    }
}
