@extends('Layout.app')

@php app()->setLocale(session('locale', 'ar')) @endphp
@section('title', __('Too many tries') . ' | اقتبس')
@section('content')
    <x-error-page code="429" :title="__('Too many tries')" :message="__('Slow down a little and try again in a minute.')"
        quote="في التأني السلامة، وفي العجلة الندامة" :said="__('Arabic proverb')" />
@endsection
