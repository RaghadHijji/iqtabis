@extends('Layout.app')

@php app()->setLocale(session('locale', 'ar')) @endphp
@section('title', __('Not allowed') . ' | اقتبس')
@section('content')
    <x-error-page code="403" :title="__('Not allowed')" :message="__('This is not yours to change. You can only edit or delete your own quotes.')"
        quote="من تدخّل فيما لا يعنيه لقي ما لا يرضيه" :said="__('Arabic proverb')" />
@endsection
