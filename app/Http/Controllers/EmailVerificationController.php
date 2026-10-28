<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
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
     * The link in the email: the account is confirmed.
     */
    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return redirect()->route('home')->with('status', __('Your email is confirmed. Welcome to Iqtabis!'));
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

        return URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), [
            'id' => $user->getKey(),
            'hash' => sha1($user->getEmailForVerification()),
        ]);
    }
}
