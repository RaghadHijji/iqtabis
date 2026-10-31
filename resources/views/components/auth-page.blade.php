@props(['title', 'intro' => null])

{{-- a small centered card for the password and email pages: an old sheet of paper on the wooden desk, like the log in pop-up --}}
<div class="wood pointer-events-none fixed inset-0 -z-10" aria-hidden="true">
    <div class="wood-lamp absolute inset-0"></div>
</div>

<section class="mx-auto flex min-h-[calc(100svh-74px)] max-w-md items-center px-6 py-16">
    <div class="auth-card vintage w-full p-8">
        <x-back-link class="mb-4 text-sm" />
        <h1 class="mb-2 text-center text-2xl font-extrabold text-ink">{{ $title }}</h1>
        @if ($intro)
            <p class="mb-6 text-center text-sm leading-relaxed text-ink/80">{{ $intro }}</p>
        @endif

        {{ $slot }}
    </div>
</section>
