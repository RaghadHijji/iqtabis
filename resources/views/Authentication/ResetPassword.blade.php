@extends('Layout.app')

@section('title', __('Choose a new password') . ' | اقتبس')
@section('content')
    <x-auth-page :title="__('Choose a new password')" :intro="__('At least 8 characters.')">
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <label class="auth-field">
                <input name="email" type="email" value="{{ old('email', $email) }}" placeholder=" " required autocomplete="email">
                <span>{{ __('Email') }}</span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2.5" y="5" width="19" height="14" rx="2"/><path d="M3 6.5l9 6.5 9-6.5"/></svg>
            </label>
            @error('email') <p class="-mt-4 mb-4 text-xs font-bold text-red-600">{{ $message }}</p> @enderror

            <label class="auth-field">
                <input name="password" type="password" placeholder=" " required minlength="8" autocomplete="new-password" autofocus>
                <span>{{ __('New password') }}</span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M7 10V7a5 5 0 0 1 10 0v3h1a1 1 0 0 1 1 1v9a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1v-9a1 1 0 0 1 1-1h1zm2 0h6V7a3 3 0 0 0-6 0v3z"/></svg>
            </label>
            @error('password') <p class="-mt-4 mb-4 text-xs font-bold text-red-600">{{ $message }}</p> @enderror

            <label class="auth-field">
                <input name="password_confirmation" type="password" placeholder=" " required minlength="8" autocomplete="new-password">
                <span>{{ __('Confirm new password') }}</span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M7 10V7a5 5 0 0 1 10 0v3h1a1 1 0 0 1 1 1v9a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1v-9a1 1 0 0 1 1-1h1zm2 0h6V7a3 3 0 0 0-6 0v3z"/></svg>
            </label>

            <button type="submit" class="mt-1 h-11 w-full rounded-xl bg-ink font-bold text-page transition hover:-translate-y-0.5 hover:shadow-lg">{{ __('Save new password') }}</button>
        </form>
    </x-auth-page>
@endsection
