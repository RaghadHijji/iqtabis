@extends('errors.plain')

@php app()->setLocale(rescue(fn () => session('locale', 'ar'), 'ar', false)) @endphp
@section('title', __('Something went wrong'))
@section('content')
    <x-error-page code="500" :title="__('Something went wrong')" :message="__('A problem happened on our side. Please try again in a moment.')"
        quote="مب كل مرة تسلم الجرة" :said="__('Arabic proverb')" />
@endsection
