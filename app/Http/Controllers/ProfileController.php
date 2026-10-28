<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Upload a new profile picture and remove the old one.
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = $request->user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->avatar = $request->file('avatar')->store('avatars', 'public');
        $user->save();

        return back()->with(['account_status' => __('Profile picture updated.'), 'account_section' => 'account-avatar']);
    }

    /**
     * Go back to the letter avatar.
     */
    public function removeAvatar(Request $request)
    {
        $user = $request->user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->avatar = null;
            $user->save();
        }

        return back()->with(['account_status' => __('Profile picture removed.'), 'account_section' => 'account-avatar']);
    }

    /**
     * Change the email (the current password is asked first).
     */
    public function updateEmail(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'current_password' => ['required', 'current_password'],
        ]);

        // a new email has to be confirmed again (FR-24)
        if ($user->email !== $data['email']) {
            $user->email = $data['email'];
            $user->email_verified_at = null;
            $user->save();
            $user->sendEmailVerificationNotification();
        }

        return back()->with(['account_status' => __('Email updated.'), 'account_section' => 'account-email']);
    }

    /**
     * Change the password (the password is hashed by the User model).
     */
    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $request->user()->update(['password' => $data['password']]);

        return back()->with(['account_status' => __('Password updated.'), 'account_section' => 'account-password']);
    }

    /**
     * Delete the account with all its quotes, likes and favorites (cascade).
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'delete_password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }
        $user->tokens()->delete();
        $user->notifications()->delete();

        Auth::logout();
        $user->delete();

        $settings = $request->session()->only(['locale', 'theme']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        session($settings);

        return redirect()->route('home')->with('status', __('Your account was deleted. Goodbye!'));
    }
}
