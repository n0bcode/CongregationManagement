<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the user's role (for testing purposes only).
     * WARNING: This should be removed in production!
     */
    public function updateRole(Request $request): RedirectResponse
    {
        // Only allow in non-production environments
        if (app()->environment('production')) {
            abort(403, 'Role changes are not allowed in production');
        }

        $request->validate([
            'role' => ['required', 'string', 'in:super_admin,general,director,secretary,member'],
        ]);

        $user = $request->user();
        $user->role = $request->input('role');
        $user->save();

        // Clear permission cache
        \Illuminate\Support\Facades\Cache::flush();

        // Force logout and login again to refresh session
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Auth::login($user);

        return Redirect::route('profile.edit')->with('status', 'Role updated successfully (Testing Mode)');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
