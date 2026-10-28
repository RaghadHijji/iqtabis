@extends('Layout.app')

@php app()->setLocale(session('locale', 'ar')) @endphp
@section('title', __('Log in first') . ' | اقتبس')
@section('content')
    <x-error-page code="401" :title="__('Log in first')" :message="__('You need to log in to see this page.')"
        quote="من دق الباب سمع الجواب" :said="__('Arabic proverb')">
        <a href="{{ url('/?auth=login') }}" class="inline-flex items-center rounded-full border border-ink/15 bg-card px-7 py-3 font-bold transition hover:border-accent hover:text-accent">{{ __('Log In') }}</a>
    </x-error-page>
@endsection
