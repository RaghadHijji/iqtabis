<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    /**
     * "Forgot your password?" page: type your email to get a reset link (FR-20).
     */
    public function create()
    {
        return view('Authentication.ForgotPassword');
    }

    /**
     * Email a reset link that works for 60 minutes.
     * The answer is the same whether the email exists or not, so nobody can find out who has an account.
     */
    public function store(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        $devLink = null;
        $status = Password::sendResetLink($request->only('email'), function (User $user, string $token) use (&$devLink) {
            $user->sendPasswordResetNotification($token);
            $devLink = route('password.reset', ['token' => $token, 'email' => $user->email]);
        });

        if ($status === Password::RESET_THROTTLED) {
            return back()->withInput()->withErrors(['email' => __($status)]);
        }

        // while developing, emails are only written to the log, so show the link on the page too
        $showDevLink = app()->isLocal() && config('mail.default') === 'log';

        return back()
            ->with('status', __('If this email has an account, we sent it a reset link.'))
            ->with('dev_link', $showDevLink ? $devLink : null);
    }

    /**
     * The page the email link opens: choose a new password.
     */
    public function edit(Request $request, string $token)
    {
        return view('Authentication.ResetPassword', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    /**
     * Save the new password, then ask the user to log in with it.
     */
    public function update(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                // the User model hashes the password (NFR-02)
                $user->forceFill(['password' => $password, 'remember_token' => Str::random(60)])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()->withInput($request->only('email'))->withErrors(['email' => __($status)]);
        }

        return redirect()->route('home', ['auth' => 'login'])->with('status', __($status));
    }
}
