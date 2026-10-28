<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class EmailVerificationController extends Controller
{
    /**
     * "Confirm your email" page, shown before sharing or commenting (FR-24).
     */
    public function notice(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('home');
        }

        return view('Authentication.VerifyEmail', ['devLink' => $this->devLink($request)]);
    }

    /**
     * The link in the email: the account is confirmed, you're logged in, and a "done" page opens.
     * The signed link is the proof (only the email's owner gets it), so it also logs you in:
     * the Gmail app opens its own browser, where you weren't logged in yet.
     * An old or changed link gets a friendly page instead of an error.
     */
    public function verify(Request $request, string $id, string $hash)
    {
        $user = $this->linkOwner($id, $hash);
        $valid = $user && $request->hasValidSignature();

        // an old link: offer to send a new one to the same email
        if (! $valid && ! $user?->hasVerifiedEmail()) {
            return response()->view('Authentication.EmailVerified', [
                'confirmed' => false,
                'resendUrl' => $user ? route('verification.resend', ['id' => $id, 'hash' => $hash]) : null,
            ], 403);
        }

        if ($valid) {
            if (! $user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
                event(new Verified($user));
            }

            if (Auth::guest()) {
                Auth::login($user);
                $request->session()->regenerate();
            }
        }

        return view('Authentication.EmailVerified', ['confirmed' => true]);
    }

    /**
     * "Send the link again" on the expired link page: the new link goes to the account's own email only.
     */
    public function resend(string $id, string $hash)
    {
        $user = $this->linkOwner($id, $hash);

        if ($user && ! $user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
        }

        return back()->with('status', __('We sent you a new confirmation link.'));
    }

    /**
     * The account a confirmation link belongs to, or null if the link was changed.
     */
    private function linkOwner(string $id, string $hash): ?User
    {
        $user = User::find($id);

        return $user && hash_equals(sha1($user->getEmailForVerification()), $hash) ? $user : null;
    }

    /**
     * Send the confirmation email again.
     */
    public function send(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('home');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', __('We sent you a new confirmation link.'));
    }

    /**
     * While developing, emails are only written to the log, so the page shows the link too.
     */
    private function devLink(Request $request): ?string
    {
        if (! app()->isLocal() || config('mail.default') !== 'log') {
            return null;
        }

        $user = $request->user();

        return URL::temporarySignedRoute('verification.verify', now()->addMinutes(config('auth.verification.expire', 60)), [
            'id' => $user->getKey(),
            'hash' => sha1($user->getEmailForVerification()),
        ]);
    }
}
