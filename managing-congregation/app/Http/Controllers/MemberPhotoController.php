<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MemberPhotoController extends Controller
{
    public function update(\App\Http\Requests\UpdateMemberPhotoRequest $request, Member $member): RedirectResponse
    {
        // Validation handled by FormRequest

        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($member->profile_photo_path) {
                Storage::disk('public')->delete($member->profile_photo_path);
            }

            $path = $request->file('photo')->store('profile-photos', 'public');

            $member->update([
                'profile_photo_path' => $path,
            ]);
        }

        return back()->with('success', 'Profile photo updated successfully.');
    }

    public function destroy(Member $member): RedirectResponse
    {
        if ($member->profile_photo_path) {
            Storage::disk('public')->delete($member->profile_photo_path);

            $member->update([
                'profile_photo_path' => null,
            ]);
        }

        return back()->with('success', 'Profile photo removed.');
    }
}
