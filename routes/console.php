<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// make an account an admin: php artisan app:make-admin you@example.com (FR-25)
Artisan::command('app:make-admin {email}', function (string $email) {
    $user = User::where('email', $email)->first();

    if (! $user) {
        $this->error("No account with the email {$email}.");

        return 1;
    }

    $user->forceFill(['is_admin' => true, 'email_verified_at' => $user->email_verified_at ?? now()])->save();
    $this->info("{$user->name} is an admin now. Open /admin after logging in.");
})->purpose('Make an account an admin');
