@extends('Layout.app')

@section('title', __('Confirm your email') . ' | اقتبس')
@section('content')
    <x-auth-page :title="__('Confirm your email')" :intro="__('We sent a confirmation link to :email. Open it to start sharing quotes and comments.', ['email' => auth()->user()->email])">
        <span class="mx-auto mb-6 grid h-16 w-16 place-items-center rounded-full bg-accent/10 text-accent"><x-icon name="mail" class="h-8 w-8" /></span>

        <p class="mb-5 text-center text-sm text-ink/70">{{ __("Can't find it? Check your Spam folder and mark it as not spam, so the button works.") }}</p>

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="h-11 w-full rounded-xl bg-ink font-bold text-page transition hover:-translate-y-0.5 hover:shadow-lg">{{ __('Send the link again') }}</button>
        </form>

        {{-- only on the developer's computer: emails go to the log, so the link is shown here --}}
        @if ($devLink)
            <div class="mt-5 rounded-2xl border border-dashed border-accent/50 bg-accent/5 p-4 text-sm text-ink/80">
                <p class="mb-2">{{ __('Development mode: emails are saved in the log, so here is the link:') }}</p>
                <a href="{{ $devLink }}" class="font-bold text-accent hover:underline">{{ __('Confirm my email') }}</a>
            </div>
        @endif

        <p class="mt-6 text-center text-sm">
            <a href="{{ route('home') }}" class="font-bold text-accent hover:underline">{{ __('Browse quotes for now') }}</a>
        </p>
    </x-auth-page>

    {{-- once the link is opened in another tab, this page notices and moves on by itself --}}
    <script>
        (() => {
            const check = () => fetch(@json(route('verification.notice')), { headers: { Accept: 'text/html' } })
                .then((response) => { if (response.redirected) location.href = response.url; })
                .catch(() => {});

            setInterval(check, 4000);
            addEventListener('focus', check);
        })();
    </script>
@endsection
