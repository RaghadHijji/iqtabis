@extends('errors.plain')

@php app()->setLocale(rescue(fn () => session('locale', 'ar'), 'ar', false)) @endphp
@section('title', __('Back soon'))
@section('content')
    <x-error-page code="503" :title="__('Back soon')" :message="__('Iqtabis is getting a little update. Come back in a few minutes.')"
        quote="الصبر مفتاح الفرج" :said="__('Arabic proverb')" :home="false" />
@endsection
