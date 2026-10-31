@props(['title', 'updated' => '2026-10-24'])

{{-- a calm reading page (privacy, terms): a back link, a title, and the text in a card --}}
<x-painting-bg />

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
