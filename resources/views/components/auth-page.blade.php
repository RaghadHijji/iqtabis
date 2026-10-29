@props(['title', 'intro' => null])

{{-- a small centered card for the password and email pages, on the arches photo like the log in pop-up --}}
<div class="pointer-events-none fixed inset-0 -z-10" aria-hidden="true">
    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('images/hero-video-poster.jpg') }}')"></div>
    <div class="absolute inset-0 bg-gradient-to-b from-black/45 via-black/30 to-black/60"></div>
</div>

<section class="mx-auto flex min-h-[calc(100svh-74px)] max-w-md items-center px-6 py-16">
    <div class="auth-card glass w-full rounded-3xl p-8 shadow-2xl">
        <x-back-link class="mb-4 text-sm" />
        <h1 class="mb-2 text-center text-2xl font-extrabold text-ink">{{ $title }}</h1>
        @if ($intro)
            <p class="mb-6 text-center text-sm leading-relaxed text-ink/80">{{ $intro }}</p>
        @endif

        {{ $slot }}
    </div>
</section>
