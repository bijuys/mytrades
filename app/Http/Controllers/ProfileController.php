<?php

namespace App\Http\Controllers;

use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
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
     * Update the user's profile information and avatar.
     */
    public function update(Request $request, ImageService $imageService): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => [
                'required',
                'string',
                'regex:/^(\+91[\-\s]?)?[6789]\d{9}$/',
            ],
            'trading_experience' => ['required', 'integer', 'min:0', 'max:60'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:5120'],
            'remove_avatar' => ['nullable', 'boolean'],
        ], [
            'phone.regex' => 'Please enter a valid 10-digit Indian mobile number (e.g. 9876543210 or +91 9876543210).',
            'avatar.max' => 'Profile picture must not exceed 5 MB.',
        ]);

        // Handle Avatar removal
        if ($request->boolean('remove_avatar')) {
            if ($user->avatar) {
                $imageService->deleteAvatar($user->avatar);
                $user->avatar = null;
            }
        }

        // Handle new Avatar upload
        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                $imageService->deleteAvatar($user->avatar);
            }
            $user->avatar = $imageService->convertAndStoreAvatar($request->file('avatar'));
        }

        $cleanPhone = preg_replace('/[^\d+]/', '', $validated['phone']);

        $user->first_name = trim($validated['first_name']);
        $user->last_name = trim($validated['last_name']);
        $user->name = trim($validated['first_name'].' '.$validated['last_name']);
        $user->email = strtolower(trim($validated['email']));
        $user->phone = $cleanPhone;
        $user->trading_experience = (int) $validated['trading_experience'];

        $user->save();

        return redirect()->route('profile.edit')->with('status', 'profile-updated')->with('success', 'Profile details updated successfully!');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.current_password' => 'The provided current password does not match our records.',
            'password.confirmed' => 'The new password confirmation does not match.',
            'password.min' => 'The new password must be at least 8 characters long.',
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile.edit')->with('status', 'password-updated')->with('success', 'Your password has been changed successfully!');
    }

    /**
     * Close / Delete the user's account and all associated data.
     */
    public function destroy(Request $request, ImageService $imageService): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'current_password'],
        ], [
            'password.current_password' => 'The password you entered is incorrect. Account could not be closed.',
        ]);

        $user = $request->user();

        // 1. Delete all trade screenshots from storage
        foreach ($user->trades as $trade) {
            if ($trade->screenshot) {
                $imageService->deleteScreenshot($trade->screenshot);
            }
        }

        // 2. Delete avatar from storage if exists
        if ($user->avatar) {
            $imageService->deleteAvatar($user->avatar);
        }

        // 3. Remove database relations
        $user->trades()->delete();
        $user->strategies()->delete();
        $user->sentMessages()->delete();
        $user->receivedMessages()->delete();

        // 4. Log out and invalidate session
        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Your account and all associated trading data have been permanently closed.');
    }
}
