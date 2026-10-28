<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showSignup()
    {
        // sign up is a pop-up on the home page now
        return redirect()->route('home', ['auth' => 'signup']);
    }

    /**
     * Create the account and log the user in (FR-02).
     */
    public function signup(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:50', 'regex:/^[\pL\s]+$/u'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // the password is hashed by the User model (NFR-02)
        $user = User::create($data);

        // sends the "confirm your email" link (FR-24)
        event(new Registered($user));

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('verification.notice')->with('status', __('Welcome to Iqtabis, :name!', ['name' => $user->name]));
    }

    public function showLogin()
    {
        // log in is a pop-up on the home page now
        return redirect()->route('home', ['auth' => 'login']);
    }

    /**
     * Log in with email and password (FR-03).
     * After 5 wrong tries for the same email and device, wait one minute (NFR-12).
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = Str::lower($credentials['email']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            abort(429, '', ['Retry-After' => RateLimiter::availableIn($throttleKey)]);
        }

        // "Remember me" keeps you logged in after the browser is closed
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey, 60);

            return back()
                ->withErrors(['email' => __('Wrong email or password.')])
                ->onlyInput('email', 'form', 'remember');
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        return redirect()->intended(route('home'))->with('status', __('Welcome back, :name!', ['name' => Auth::user()->name]));
    }

    /**
     * Log out and go back to the guest view (FR-04).
     */
    public function logout(Request $request)
    {
        // keep the chosen language and theme after logging out
        $settings = $request->session()->only(['locale', 'theme']);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        session($settings);

        return redirect()->route('home')->with('status', __('You logged out. See you soon!'));
    }
}
