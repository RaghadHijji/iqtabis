{{-- the site footer: who we are, the categories, quick links, and the made-by line --}}
@php
    $categories = \App\Models\Category::orderBy('position')->get();
    $arabic = app()->getLocale() === 'ar';
    $link = 'transition hover:text-accent hover:underline';

    // Raghad's own accounts, shown next to the made-by line
    $socials = [
        'GitHub' => [
            'url' => 'https://github.com/RaghadHijji',
            'hover' => 'hover:text-ink',
            'icon' => '<path d="M12 .5a11.5 11.5 0 0 0-3.64 22.4c.58.1.79-.25.79-.56v-2c-3.2.7-3.88-1.37-3.88-1.37-.52-1.33-1.28-1.69-1.28-1.69-1.05-.71.08-.7.08-.7 1.16.08 1.77 1.19 1.77 1.19 1.03 1.77 2.7 1.26 3.36.96.1-.75.4-1.26.73-1.55-2.55-.29-5.24-1.28-5.24-5.69 0-1.26.45-2.29 1.19-3.1-.12-.29-.52-1.46.11-3.05 0 0 .97-.31 3.17 1.18a11 11 0 0 1 5.78 0c2.2-1.49 3.17-1.18 3.17-1.18.63 1.59.23 2.76.11 3.05.74.81 1.19 1.84 1.19 3.1 0 4.42-2.7 5.39-5.26 5.68.41.36.78 1.06.78 2.14v3.17c0 .31.21.67.8.56A11.5 11.5 0 0 0 12 .5z"/>',
        ],
        'LinkedIn' => [
            'url' => 'https://www.linkedin.com/in/raghad-ahmad-661540370',
            'hover' => 'hover:text-accent',
            'icon' => '<path d="M20.45 20.45h-3.56v-5.57c0-1.33-.02-3.04-1.85-3.04-1.85 0-2.14 1.45-2.14 2.94v5.67H9.34V9h3.41v1.56h.05c.48-.9 1.64-1.85 3.37-1.85 3.6 0 4.27 2.37 4.27 5.46v6.28zM5.34 7.43a2.06 2.06 0 1 1 0-4.13 2.06 2.06 0 0 1 0 4.13zM7.12 20.45H3.56V9h3.56v11.45zM22.22 0H1.77C.79 0 0 .77 0 1.73v20.54C0 23.23.79 24 1.77 24h20.45c.98 0 1.78-.77 1.78-1.73V1.73C24 .77 23.2 0 22.22 0z"/>',
        ],
    ];
@endphp

<footer class="mt-12 border-t border-ink/10 bg-card text-sm text-ink/70">
    {{-- three equal columns: the name at the start, the categories in the middle, explore at the end --}}
    <div class="mx-auto grid max-w-6xl gap-6 px-6 py-8 md:grid-cols-3 md:items-start">
        <div class="flex flex-col gap-1">
            <p class="flex items-baseline gap-3">
                <span class="font-brand text-3xl font-bold leading-none text-ink">اقتبس</span>
                <span class="text-[10px] font-bold tracking-[0.35em] text-sand">IQTABIS</span>
            </p>
            <p class="{{ $arabic ? 'font-brand text-lg' : 'font-display italic' }} text-ink">{{ __('Here the page ends, not the words') }}</p>
        </div>

        <nav aria-label="{{ __('Categories') }}" class="md:max-w-xs md:justify-self-center md:text-center">
            <h2 class="mb-2 font-bold text-ink">{{ __('Categories') }}</h2>
            <ul class="flex flex-wrap gap-x-4 gap-y-1 md:justify-center">
                @foreach ($categories as $category)
                    <li><a href="{{ route('home', ['filter' => $category->name]) }}#quotes" class="{{ $link }}">{{ __($category->name) }}</a></li>
                @endforeach
            </ul>
        </nav>

        <nav aria-label="{{ __('Explore') }}" class="md:max-w-60 md:justify-self-end md:text-center">
            <h2 class="mb-2 font-bold text-ink">{{ __('Explore') }}</h2>
            <ul class="flex flex-wrap gap-x-4 gap-y-1 md:justify-center">
                <li><a href="{{ route('home') }}#quotes" class="{{ $link }}">{{ __('All quotes') }}</a></li>
                <li><a href="{{ route('home', ['sort' => 'liked']) }}#quotes" class="{{ $link }}">{{ __('Most liked') }}</a></li>
                <li><a href="{{ route('quotes.random') }}" class="{{ $link }}">{{ __('Quote me') }}</a></li>
                @guest
                    <li><a href="{{ route('home') }}#about" class="{{ $link }}">{{ __('About Iqtabis') }}</a></li>
                @else
                    <li><a href="{{ route('home', ['by' => auth()->id()]) }}#quotes" class="{{ $link }}">{{ __('My page') }}</a></li>
                @endguest
            </ul>
        </nav>
    </div>

    <div id="footer-bar" class="border-t border-ink/10">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3 px-6 py-4 text-xs">
            <p dir="ltr">© {{ now()->year }} Iqtabis | اقتبس</p>
            <div class="flex items-center gap-3">
                <p>{{ __('Made by Raghad Hijji · Tuwaiq Bootcamp 2026') }}</p>
                @foreach ($socials as $name => $social)
                    <a href="{{ $social['url'] }}" target="_blank" rel="noopener" title="{{ $name }}" aria-label="{{ $name }}" class="text-ink/70 transition {{ $social['hover'] }}">
                        <svg viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4" aria-hidden="true">{!! $social['icon'] !!}</svg>
                    </a>
                @endforeach
            </div>
            <p class="flex gap-4">
                @if ($supportEmail = config('app.support_email'))
                    <a href="mailto:{{ $supportEmail }}" class="{{ $link }}">{{ __('Support') }}</a>
                @endif
                <a href="{{ route('privacy') }}" class="{{ $link }}">{{ __('Privacy') }}</a>
                <a href="{{ route('terms') }}" class="{{ $link }}">{{ __('Terms') }}</a>
            </p>
        </div>
    </div>
</footer>
