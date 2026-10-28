@extends('Layout.app')

@section('title', __('Forgot your password?') . ' | اقتبس')
@section('content')
    <x-auth-page :title="__('Forgot your password?')" :intro="__('Type the email of your account and we will send you a link to choose a new password.')">
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <label class="auth-field">
                <input name="email" type="email" value="{{ old('email') }}" placeholder=" " required autofocus
                    pattern="[^@\s]+@[^@\s]+\.[A-Za-z]{2,}" autocomplete="email">
                <span>{{ __('Email') }}</span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2.5" y="5" width="19" height="14" rx="2"/><path d="M3 6.5l9 6.5 9-6.5"/></svg>
            </label>
            @error('email') <p class="-mt-4 mb-4 text-xs font-bold text-red-600">{{ $message }}</p> @enderror

            <button type="submit" class="mt-1 h-11 w-full rounded-xl bg-ink font-bold text-page transition hover:-translate-y-0.5 hover:shadow-lg">{{ __('Send reset link') }}</button>
        </form>

        {{-- only on the developer's computer: emails go to the log, so the link is shown here --}}
        @if (session('dev_link'))
            <div class="mt-5 rounded-2xl border border-dashed border-accent/50 bg-accent/5 p-4 text-sm text-ink/80">
                <p class="mb-2">{{ __('Development mode: emails are saved in the log, so here is the link:') }}</p>
                <a href="{{ session('dev_link') }}" class="font-bold text-accent hover:underline">{{ __('Open the reset link') }}</a>
            </div>
        @endif

        <p class="mt-6 text-center text-sm">
            <a href="{{ route('login') }}" class="font-bold text-accent hover:underline">{{ __('Back to log in') }}</a>
        </p>
    </x-auth-page>
@endsection
