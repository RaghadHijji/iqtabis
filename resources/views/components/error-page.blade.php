{{-- one look for every error page: the code, what happened, and a quote that says it best --}}
@props(['code', 'title', 'message', 'quote', 'said', 'home' => true])

<section class="mx-auto flex min-h-[60vh] max-w-xl flex-col items-center justify-center gap-5 px-6 py-20 text-center">
    <span class="font-brand text-8xl font-bold text-accent">{{ $code }}</span>
    <h1 class="text-3xl font-extrabold text-ink">{{ $title }}</h1>
    <p class="text-lg text-ink/70">{{ $message }}</p>

    <figure class="mt-2 flex flex-col gap-2 rounded-3xl border border-ink/10 bg-card px-8 py-6 shadow-sm">
        <blockquote class="font-quote text-2xl leading-relaxed text-ink" dir="rtl">«{{ $quote }}»</blockquote>
        <figcaption class="text-sm font-bold text-sand" dir="rtl">— {{ $said }}</figcaption>
    </figure>

    <div class="mt-2 flex flex-wrap justify-center gap-3">
        {{ $slot }}
        @if ($home)
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 rounded-full bg-accent px-7 py-3 font-bold text-on-accent shadow-lg transition hover:-translate-y-0.5 hover:brightness-110">
                {{ __('Back to the quotes') }}
            </a>
        @endif
    </div>
</section>
