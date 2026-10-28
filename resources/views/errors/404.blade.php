@extends('Layout.app')

{{-- a missing page skips the language middleware, so pick the language here (Arabic by default) --}}
@php app()->setLocale(session('locale', 'ar')) @endphp
@section('title', __('Page not found') . ' | اقتبس')
@section('content')
    <x-error-page code="404" :title="__('Page not found')" :message="__('The page you are looking for does not exist, or it was moved.')"
        quote="ما كلُّ ما يتمنى المرءُ يدركه" said="المتنبي" />
@endsection
