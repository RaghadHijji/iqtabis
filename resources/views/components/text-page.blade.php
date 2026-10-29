@props(['title', 'updated' => '2026-10-24'])

{{-- a calm reading page (privacy, terms): a back link, a title, and the text in a card --}}
<div class="page-photo pointer-events-none fixed inset-0 -z-10" aria-hidden="true">
    <div class="absolute inset-0 scale-110 bg-cover bg-center photo-warm" style="background-image: url('{{ asset('images/hero-video-poster.jpg') }}')"></div>
    <div class="absolute inset-0 bg-gradient-to-b from-page/60 via-page/70 to-page/85"></div>
</div>

<article class="mx-auto flex max-w-3xl flex-col gap-6 px-6 py-12">
    <x-back-link class="self-start" />
    <div class="rounded-3xl border border-ink/10 bg-card p-8 text-ink shadow-sm md:p-12">
        <h1 class="text-3xl font-extrabold">{{ $title }}</h1>
        <p class="mt-2 text-sm text-ink/70">{{ __('Last updated: :date', ['date' => \Illuminate\Support\Carbon::parse($updated)->translatedFormat('j F Y')]) }}</p>
        <div class="legal mt-8 flex flex-col gap-6 leading-loose">
            {{ $slot }}
        </div>
    </div>
</article>
