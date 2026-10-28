@extends('Layout.app')

@php app()->setLocale(session('locale', 'ar')) @endphp
@section('title', __('Page expired') . ' | اقتبس')
@section('content')
    <x-error-page code="419" :title="__('Page expired')" :message="__('The page was open for too long. Go back, refresh it and try again.')"
        quote="الوقت كالسيف إن لم تقطعه قطعك" :said="__('Arabic proverb')" />
@endsection
