<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\RedirectResponse;
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

            $file = $request->file('photo');
            $filename = $file->hashName();
            // Change extension to webp
            $filename = pathinfo($filename, PATHINFO_FILENAME).'.webp';
            $path = 'profile-photos/'.$filename;

            // Optimize image
            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver);
            $image = $manager->read($file);

            // Resize to max 800x800, keeping aspect ratio
            $image->scale(width: 800, height: 800);

            // Encode to WebP with 85% quality
            $encoded = $image->toWebp(quality: 85);

            // Store optimized image
            Storage::disk('public')->put($path, (string) $encoded);

            $member->update([
                'profile_photo_path' => $path,
            ]);
        }

        return back()->with('success', 'Profile photo updated successfully.');
    }

    public function destroy(Member $member): RedirectResponse
    {
        \Illuminate\Support\Facades\Gate::authorize('update', $member);

        if ($member->profile_photo_path) {
            Storage::disk('public')->delete($member->profile_photo_path);

            $member->update([
                'profile_photo_path' => null,
            ]);
        }

        return back()->with('success', 'Profile photo removed.');
    }
}
