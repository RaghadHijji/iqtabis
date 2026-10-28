@extends('Layout.app')

@section('title', ($confirmed ? __('Email confirmed') : __('This link has expired')) . ' | اقتبس')
@section('content')
    @if ($confirmed)
        <x-auth-page :title="__('Email confirmed')" :intro="__('Welcome to Iqtabis! You can now share your quotes and comment.')">
            <span class="mx-auto mb-6 grid h-16 w-16 place-items-center rounded-full bg-accent/10 text-accent"><x-icon name="check" class="h-8 w-8" /></span>

            <a href="{{ route('home') }}" class="grid h-11 w-full place-items-center rounded-xl bg-ink font-bold text-page transition hover:-translate-y-0.5 hover:shadow-lg">{{ __('Go to Iqtabis') }}</a>
        </x-auth-page>
    @else
        {{-- the link is older than 10 minutes, or it was changed --}}
        @php($intro = __('Confirmation links work for 10 minutes. Press the button and a new link will reach your email.'))
        <x-auth-page :title="__('This link has expired')" :intro="$intro">
            <span class="mx-auto mb-6 grid h-16 w-16 place-items-center rounded-full bg-accent/10 text-accent"><x-icon name="mail" class="h-8 w-8" /></span>

            @if ($resendUrl)
                <form method="POST" action="{{ $resendUrl }}">
                    @csrf
                    <button type="submit" class="h-11 w-full rounded-xl bg-ink font-bold text-page transition hover:-translate-y-0.5 hover:shadow-lg">{{ __('Send the link again') }}</button>
                </form>
            @else
                <a href="{{ route('home') }}" class="grid h-11 w-full place-items-center rounded-xl bg-ink font-bold text-page transition hover:-translate-y-0.5 hover:shadow-lg">{{ __('Go to Iqtabis') }}</a>
            @endif
        </x-auth-page>
    @endif
@endsection
