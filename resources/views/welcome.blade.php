@extends('Layout.app')

@section('title', 'Iqtabis | اقتبس')
@section('content')
    @php
        // keep the filter, search, view and sort in every link
        $state = array_filter(['filter' => $filter, 'q' => $search, 'view' => $view, 'sort' => $sort, 'by' => $poster?->id]);
        $editingId = request()->integer('edit');
        $browsing = ! $filter && $search === '' && ! $view && ! $poster; // the plain home page

        // icons (inline SVG, they take the text color)
        $quoteMark = '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M4 18v-5c0-3.3 1.8-6 5-7l.8 1.6C7.9 8.4 7 9.8 7 11h3v7H4zm10 0v-5c0-3.3 1.8-6 5-7l.8 1.6c-1.9.8-2.8 2.2-2.8 3.4h3v7h-6z"/></svg>';
        $heart = '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 21s-7.5-4.6-9.6-9.2C.9 8.4 3 4.5 6.7 4.5c2 0 3.6 1.1 4.4 2.6.8-1.5 2.4-2.6 4.4-2.6 3.7 0 5.8 3.9 4.3 7.3C19.5 16.4 12 21 12 21z"/></svg>';
        $heartLine = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 21s-7.5-4.6-9.6-9.2C.9 8.4 3 4.5 6.7 4.5c2 0 3.6 1.1 4.4 2.6.8-1.5 2.4-2.6 4.4-2.6 3.7 0 5.8 3.9 4.3 7.3C19.5 16.4 12 21 12 21z"/></svg>';
        $bookmark = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 21l-7-4-7 4V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>';
        $bookmarkFull = '<svg viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linejoin="round" aria-hidden="true"><path d="M19 21l-7-4-7 4V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>';
        $copy = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15V5a2 2 0 0 1 2-2h10"/></svg>';
        $pen = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>';
        $flag = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 22V4M4 4h13l-2 4 2 4H4"/></svg>';
        $trash = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6"/></svg>';
    @endphp

    {{-- Opening screen: a full-screen picture with the quote of the day on it --}}
    @if ($browsing && $quoteOfDay)
        <section id="hero" class="relative -mt-[74px] flex min-h-svh items-center justify-center overflow-hidden bg-bar text-white">
            <div id="hero-bg" class="absolute inset-x-0 -top-[10%] h-[120%] bg-cover bg-center" style="background-image: url('{{ asset('images/hero.jpg') }}')"></div>
            <div class="absolute inset-0 bg-gradient-to-b from-black/55 via-black/35 to-black/75"></div>

            <div id="hero-content" class="hero-in relative mx-auto flex max-w-4xl flex-col items-center gap-6 px-6 pb-36 pt-28 text-center">
                <p class="{{ app()->getLocale() === 'ar' ? 'font-brand text-3xl' : 'font-display text-2xl italic' }} text-white/85">{{ __('A quote for every moment') }}</p>
                <span class="h-14 w-14 text-[#E8CFAE]/80">{!! $quoteMark !!}</span>
                <blockquote class="font-quote text-4xl leading-snug drop-shadow-lg md:text-6xl md:leading-snug" dir="auto">{{ $quoteOfDay->content }}</blockquote>
                <p class="text-lg text-white/80">— {{ $quoteOfDay->author ?: $quoteOfDay->user->name }} · {{ __($quoteOfDay->category->name) }}</p>
                {{-- today in both calendars --}}
                <p class="text-sm font-bold text-white/90 drop-shadow">{{ now()->hijri(true) }} · {{ now()->translatedFormat('j F Y') }}</p>
                <div class="flex items-center gap-3">
                    <span class="rounded-full bg-white/15 px-4 py-2 text-sm font-bold tracking-[0.2em] backdrop-blur rtl:tracking-normal">{{ __('QUOTE OF THE DAY') }}</span>
                    <span class="flex items-center gap-1.5 rounded-full bg-white/15 px-4 py-2 font-bold backdrop-blur"><span class="h-4 w-4 text-rose-300">{!! $heart !!}</span>{{ $quoteOfDay->likes_count }}</span>
                    <button type="button" data-copy="{{ $quoteOfDay->content }}" data-author="{{ $quoteOfDay->author }}" title="{{ __('Copy quote') }}" aria-label="{{ __('Copy quote') }}"
                        class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-white/15 backdrop-blur transition hover:bg-white/30"><span class="h-[18px] w-[18px]">{!! $copy !!}</span></button>
                </div>
            </div>

            {{-- scroll down to see the rest --}}
            <a href="#explore" class="absolute bottom-24 sm:bottom-16 flex flex-col items-center gap-1 text-sm text-white/80 transition hover:text-white">
                {{ __('Scroll to explore') }}
                <svg class="h-6 w-6 animate-bounce" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
            </a>
        </section>

        {{-- a slow strip of the authors on the site; pressing a name shows their quotes --}}
        @if ($authors->count() >= 3)
            <nav aria-label="{{ __('Authors') }}" class="marquee overflow-hidden border-y border-ink/10 bg-card py-3 text-ink">
                <div class="marquee-track flex w-max">
                    @foreach ([false, true] as $repeat)
                        <ul class="flex shrink-0 items-center" @if ($repeat) aria-hidden="true" @endif>
                            @foreach ($authors as $author)
                                <li class="flex items-center">
                                    <a href="{{ route('home', ['q' => $author]) }}#quotes" @if ($repeat) tabindex="-1" @endif
                                        class="whitespace-nowrap px-6 font-bold transition hover:text-accent hover:underline">{{ $author }}</a>
                                    <span class="h-4 w-4 text-accent">{!! $quoteMark !!}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endforeach
                </div>
            </nav>
        @endif
    @endif

    {{-- the arches photo stays behind the rest of the page, soft and light, so it keeps the same feel --}}
    <div class="page-photo pointer-events-none fixed inset-0 -z-10" aria-hidden="true">
        <div class="absolute inset-0 scale-110 bg-cover bg-center blur-md" style="background-image: url('{{ asset('images/hero.jpg') }}')"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-page/60 via-page/70 to-page/85"></div>
    </div>

    {{-- members find the same text in "My account", so this shows for guests only --}}
    @if ($browsing && auth()->guest())
        {{-- About Iqtabis: a screen of its own between the opening picture and the slogan.
             No card here (the "Join" box below is a card), just the heading, the story, the site's real numbers and what it offers
             (the made-by line is in the footer) --}}
        @php
            $stats = [
                [$categories->sum('quotes_count'), __('Quotes')],
                [$categories->count(), __('categories')],
                [\App\Models\User::count(), __('members')],
                [\Illuminate\Support\Facades\DB::table('likes')->count(), __('likes given')],
            ];
            $offers = [
                ['check', __('The quotes'), __('Quotes that inspire you and touch your heart.')],
                ['heart', __('The categories'), __('Browse by your mood, from motivation to love and wisdom.')],
                ['users', __('The members'), __('Share your quotes and connect with people who love words.')],
            ];
        @endphp
        <section id="about" class="reveal mx-auto flex min-h-svh max-w-4xl scroll-mt-24 flex-col items-center justify-center gap-8 px-6 py-16 text-center">
            <div class="flex flex-col items-center gap-3">
                <h2 class="{{ app()->getLocale() === 'ar' ? 'font-quote text-4xl md:text-5xl leading-tight' : 'font-display text-4xl italic leading-tight' }} font-bold text-ink">{{-- the word اقتبس in the logo's Ruqaa script --}}{!! str_replace('اقتبس', '<span class="font-brand relative top-[0.42em]">اقتبس</span>', e(__('About Iqtabis'))) !!}</h2>
                <span class="h-1 w-16 rounded-full bg-accent"></span>
            </div>

            <p class="max-w-2xl font-quote text-xl leading-loose text-ink md:text-2xl md:leading-loose">
                <span class="text-accent">❝</span>
                {{ __('Iqtabis is a home for quotes: find words for every moment, browse by category, like and save the ones that touch you, and share your own.') }}
                <span class="text-accent">❞</span>
            </p>

            {{-- the site's real numbers --}}
            <dl class="grid w-full grid-cols-2 gap-6 md:grid-cols-4">
                @foreach ($stats as [$number, $label])
                    <div class="flex flex-col items-center gap-1">
                        <dt class="order-2 text-sm text-ink/70">{{ $label }}</dt>
                        <dd class="order-1 text-3xl font-extrabold text-accent">{{ $number }}</dd>
                    </div>
                @endforeach
            </dl>

            {{-- what the site offers, on one coloured band --}}
            <div class="w-full rounded-3xl bg-accent px-6 py-6 text-on-accent shadow-lg md:px-10">
                <ul class="grid gap-6 {{ config('app.support_email') ? 'md:grid-cols-4' : 'md:grid-cols-3' }}">
                    @foreach ($offers as [$icon, $title, $text])
                        <li class="flex flex-col items-center gap-2 text-sm"><x-icon :name="$icon" class="h-6 w-6" />
                            <span class="text-base font-bold">{{ $title }}</span>{{ $text }}</li>
                    @endforeach
                    @if ($supportEmail = config('app.support_email'))
                        <li class="flex flex-col items-center gap-2 text-sm"><x-icon name="mail" class="h-6 w-6" />
                            <span class="text-base font-bold">{{ __('Questions or ideas?') }}</span><a href="mailto:{{ $supportEmail }}" class="underline" dir="ltr">{{ $supportEmail }}</a></li>
                    @endif
                </ul>
            </div>
        </section>
    @endif

    {{-- Join the community (guests only): text on one side, a fan of real quote cards on the other --}}
    @if (auth()->guest() && $browsing && $trending->isNotEmpty())
        <section id="join" class="reveal mx-auto max-w-6xl px-6 pt-16">
            <div class="grid items-center gap-10 rounded-[2rem] border border-ink/10 bg-card p-8 shadow-sm md:grid-cols-2 md:p-12">
                <div class="flex flex-col items-start gap-5">
                    <h2 class="{{ app()->getLocale() === 'ar' ? 'font-quote text-5xl md:text-6xl leading-tight' : 'font-display text-5xl italic leading-tight' }} font-bold text-ink">{{ __('Join the Iqtabis community') }}</h2>
                    <p class="text-lg leading-loose text-ink/70">{{ __('A space for lovers of words and quotes: share your favorite quotes, discover what others picked, and keep the words that feel like you.') }}</p>
                    <a href="{{ route('signup') }}"
                        class="mt-2 inline-flex items-center gap-3 rounded-full bg-ink px-7 py-4 font-bold text-page shadow-lg transition hover:-translate-y-0.5 hover:bg-accent hover:text-on-accent hover:shadow-xl">
                        {{ __('Be part of Iqtabis') }}
                        <svg class="h-5 w-5 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </a>
                </div>

                {{-- a slow carousel of real quotes: every few seconds the next card comes to the front --}}
                <div class="fan relative h-80 rounded-3xl bg-gradient-to-br from-sand/15 via-card/40 to-accent/10 md:h-96" aria-hidden="true">
                    @foreach ($trending->concat($quotes)->unique('id')->take(6)->values() as $quote)
                        <article data-fan-card class="fan-card {{ ['pos-front', 'pos-right', 'pos-left'][$loop->index] ?? 'pos-hidden' }} absolute inset-x-[8%] top-1/2 flex flex-col gap-4 rounded-2xl border border-ink/5 bg-card p-6 text-ink shadow-2xl">
                            <div class="flex items-center gap-3">
                                <x-avatar :user="$quote->user" size="h-11 w-11 text-base" />
                                <span class="font-bold text-sand">{{ $quote->user->name }}</span>
                            </div>
                            <p class="font-quote line-clamp-3 text-2xl leading-relaxed" dir="auto">{{ $quote->content }}</p>
                            @if ($quote->author)
                                <p class="-mt-2 text-sm text-ink/70" dir="auto">— {{ $quote->author }}</p>
                            @endif
                            <div class="flex items-center gap-1.5 border-t border-ink/10 pt-3 text-sm text-ink/70">
                                <span class="h-4 w-4">{!! $heartLine !!}</span>{{ $quote->likes_count }}
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Slogan and search --}}
    <header id="explore" class="scroll-mt-24">
        <div class="mx-auto flex max-w-3xl flex-col items-center gap-5 px-6 pb-10 pt-16 text-center">
            {{-- a sister line to the slogan, so it isn't repeated: Ruqaa script in Arabic, Playfair italic in English --}}
            <h1 class="{{ app()->getLocale() === 'ar' ? 'font-brand text-5xl md:text-6xl leading-snug' : 'font-display text-5xl italic md:text-6xl leading-tight' }} font-bold">{{ __('Find a quote that matches your moment') }}</h1>

            {{-- Search (FR-11) --}}
            <form id="search-form" data-soft method="GET" action="{{ route('home') }}" class="flex w-full gap-2 rounded-2xl border border-ink/10 bg-card p-1.5 shadow-sm transition focus-within:border-accent focus-within:ring-4 focus-within:ring-accent/30">
                @foreach (Arr::except($state, 'q') as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <label for="search" class="sr-only">{{ __('Search quotes…') }}</label>
                <input id="search" type="search" name="q" value="{{ $search }}" placeholder="{{ __('Search quotes…') }}" dir="auto"
                    class="min-w-0 flex-1 bg-transparent px-3 text-ink outline-none placeholder:text-ink/60">
                <button type="submit" class="rounded-xl bg-accent px-6 py-3 font-bold text-on-accent transition hover:brightness-110">{{ __('Search') }}</button>
            </form>
        </div>
    </header>

    <div class="mx-auto flex max-w-6xl flex-col gap-14 px-6 pb-28">

        {{-- Categories with counts (FR-10) --}}
        <nav id="categories" aria-label="{{ __('Categories') }}"
            class="grid grid-cols-2 gap-3 rounded-3xl border border-ink/10 bg-card p-4 shadow-sm sm:grid-cols-4 lg:grid-cols-8">
            @php $chip = 'flex min-h-16 flex-col justify-center rounded-2xl border px-4 py-2 transition duration-200'; @endphp
            <a data-soft href="{{ route('home', Arr::except($state, 'filter')) }}"
                class="{{ $chip }} {{ $filter ? 'border-ink/10 bg-page text-ink hover:border-accent' : 'border-accent bg-accent text-on-accent' }}">
                <span class="font-bold">{{ __('All') }}</span>
                <span class="text-xs opacity-70">{{ __(':count quotes', ['count' => $totalQuotes]) }}</span>
            </a>
            @foreach ($categories as $cat)
                <a data-soft href="{{ route('home', [...$state, 'filter' => $cat->name]) }}"
                    class="{{ $chip }} {{ $filter === $cat->name ? 'border-accent bg-accent text-on-accent' : 'border-ink/10 bg-page text-ink hover:border-accent' }}">
                    <span class="font-bold">{{ __($cat->name) }}</span>
                    <span class="text-xs opacity-70">{{ __(':count quotes', ['count' => $cat->quotes_count]) }}</span>
                </a>
            @endforeach
        </nav>

        {{-- Trending: the most liked quotes (FR-08) --}}
        {{-- the slot is always there (display: contents), so a category click can swap it in place --}}
        <div id="trending-slot" class="contents">
        @if ($search === '' && ! $view && ! $poster && $trending->isNotEmpty())
            <section id="trending" class="flex flex-col gap-5">
                <div>
                    <h2 class="text-3xl font-extrabold text-ink">{{ __('Trending') }}</h2>
                    <p class="text-ink/70">
                        {{ $filter ? __('The most liked :category quotes.', ['category' => __($filter)]) : __('The most liked quotes right now.') }}
                    </p>
                </div>
                <div class="grid gap-5 md:grid-cols-3">
                    @foreach ($trending as $quote)
                        <article class="flex flex-col gap-4 rounded-3xl border border-ink/10 bg-card p-7 text-ink shadow-sm transition hover:shadow-md">
                            <div class="flex items-center gap-3">
                                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-accent font-extrabold text-on-accent">{{ $loop->iteration }}</span>
                                <span class="text-sm font-bold tracking-[0.15em] text-accent rtl:tracking-normal">{{ __($quote->category->name) }}</span>
                            </div>
                            <p class="font-quote flex-1 text-2xl leading-relaxed" dir="auto">{{ $quote->content }}</p>
                            <div class="flex items-center justify-between text-sm text-ink/70">
                                <span dir="auto">— {{ $quote->author ?: $quote->user->name }}</span>
                                <span class="flex items-center gap-1.5 font-bold text-rose-500"><span class="h-4 w-4">{!! $heart !!}</span>{{ $quote->likes_count }}</span>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif
        </div>

        {{-- All quotes --}}
        <section id="quotes" class="flex flex-col gap-5">
            {{-- the poster's page: who they are and how many likes their quotes got (FR-21) --}}
            @if ($poster)
                <div class="flex flex-wrap items-center gap-5 rounded-3xl border border-ink/10 bg-card p-6 shadow-sm">
                    <x-avatar :user="$poster" size="h-20 w-20 text-3xl" />
                    <div class="me-auto flex flex-col gap-1">
                        <p class="text-2xl font-extrabold text-ink">{{ $poster->name }}</p>
                        <p class="text-sm text-ink/70">{{ __('Joined :date', ['date' => $poster->created_at->translatedFormat('F Y')]) }}</p>
                        @if (auth()->id() === $poster->id)
                            <p class="text-xs font-bold text-accent">{{ __('This is how others see your page.') }}</p>
                        @endif
                    </div>
                    <div class="flex gap-3">
                        <div class="min-w-24 rounded-2xl bg-page px-4 py-3 text-center">
                            <p class="text-2xl font-extrabold text-accent">{{ $posterStats['quotes'] }}</p>
                            <p class="text-xs text-ink/70">{{ __('Quotes') }}</p>
                        </div>
                        <div class="min-w-24 rounded-2xl bg-page px-4 py-3 text-center">
                            <p class="text-2xl font-extrabold text-rose-500">{{ $posterStats['likes'] }}</p>
                            <p class="text-xs text-ink/70">{{ __('Likes received') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-3xl font-extrabold text-ink">
                    @if ($poster)
                        {{ __('Quotes by :name', ['name' => $poster->name]) }}
                        <a data-soft href="{{ route('home', Arr::except($state, 'by')) }}" class="ms-2 inline-block whitespace-nowrap align-middle text-sm font-bold text-accent hover:underline">{{ __('See all quotes') }}</a>
                    @else
                        {{ match ($view) { 'favs' => __('Saved'), 'likes' => __('My likes'), 'mine' => __('My quotes'), default => __('All quotes') } }}
                    @endif
                </h2>

                <div class="flex flex-wrap items-center gap-2">
                    {{-- "Quote me": a random quote, from the chosen category if there is one --}}
                    <a href="{{ route('quotes.random', array_filter(['filter' => $filter])) }}" title="{{ __('A random quote') }}"
                        class="inline-flex h-11 items-center gap-2 rounded-xl border border-ink/15 bg-card px-4 font-bold text-ink transition hover:border-accent hover:text-accent">
                        <x-icon name="shuffle" class="h-4 w-4" />{{ __('Quote me') }}
                    </a>

                    @auth
                        @php
                            $tab = 'rounded-lg px-4 py-2 text-sm transition';
                            $tabOn = 'bg-card font-bold text-ink shadow';
                            $tabOff = 'text-ink/70 hover:text-ink';
                        @endphp
                        <div class="flex gap-1 rounded-xl bg-ink/10 p-1">
                            <a data-soft href="{{ route('home', Arr::except($state, ['view', 'by'])) }}" class="{{ $tab }} {{ $view || $poster ? $tabOff : $tabOn }}">{{ __('All') }}</a>
                            <a data-soft href="{{ route('home', [...Arr::except($state, 'by'), 'view' => 'mine']) }}" class="{{ $tab }} {{ $view === 'mine' ? $tabOn : $tabOff }}">{{ __('My quotes') }}</a>
                            <a data-soft href="{{ route('home', [...Arr::except($state, 'by'), 'view' => 'likes']) }}" class="{{ $tab }} {{ $view === 'likes' ? $tabOn : $tabOff }}">{{ __('My likes') }} ({{ count($likedIds) }})</a>
                            <a data-soft href="{{ route('home', [...Arr::except($state, 'by'), 'view' => 'favs']) }}" class="{{ $tab }} {{ $view === 'favs' ? $tabOn : $tabOff }}">{{ __('Saved') }} ({{ count($favIds) }})</a>
                        </div>
                    @endauth

                    {{-- Sort: newest or most liked --}}
                    <form data-soft method="GET" action="{{ route('home') }}" class="flex items-center gap-2">
                        @foreach (Arr::except($state, 'sort') as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <label for="sort" class="text-sm text-ink/70">{{ __('Sort') }}</label>
                        {{-- the box is as wide as the chosen word, so the arrow sits right next to it --}}
                        <span class="relative">
                            <select id="sort" name="sort" onchange="this.form.requestSubmit()"
                                class="field-sizing-content h-11 cursor-pointer appearance-none rounded-xl border border-ink/15 bg-card pe-9 ps-4 text-ink">
                                <option value="">{{ __('Newest') }}</option>
                                <option value="liked" @selected($sort === 'liked')>{{ __('Most liked') }}</option>
                            </select>
                            <svg class="pointer-events-none absolute end-3 top-1/2 h-4 w-4 -translate-y-1/2 text-ink/70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
                        </span>
                    </form>
                </div>
            </div>

            @if ($search !== '')
                <p class="text-ink/70">
                    {{ __('Results for ":q"', ['q' => $search]) }} ·
                    <a data-soft href="{{ route('home', Arr::except($state, 'q')) }}" class="font-bold text-accent hover:underline">{{ __('Clear') }}</a>
                </p>
            @endif

            {{-- validation errors (e.g. quote too short) --}}
            @if ($errors->any() && ! old('form'))
                <div class="rounded-xl border border-red-300 bg-red-50 p-3 text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($quotes as $quote)
                    @if ($quote->id === $editingId && auth()->id() === $quote->user_id)
                        {{-- Edit form (FR-06) --}}
                        <form method="POST" action="{{ route('quotes.update', [$quote, ...$state, 'page' => $quotes->currentPage()]) }}"
                            class="flex flex-col gap-3 rounded-3xl border-2 border-accent bg-card p-6">
                            @csrf
                            @method('PUT')
                            <select name="category_id" required class="input">
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" @selected($cat->id === $quote->category_id)>{{ __($cat->name) }}</option>
                                @endforeach
                            </select>
                            <textarea name="content" rows="4" required minlength="3" maxlength="155" class="input font-quote text-lg" dir="auto"
                                autofocus>{{ old('content', $quote->content) }}</textarea>
                            <input type="text" name="author" maxlength="80" value="{{ old('author', $quote->author) }}" dir="auto"
                                placeholder="{{ __('Who said it? (optional)') }}" class="input">
                            <div class="flex gap-2">
                                <button type="submit" class="btn-secondary">{{ __('Save') }}</button>
                                <a href="{{ route('home', [...$state, 'page' => $quotes->currentPage()]) }}" class="px-4 py-2 font-bold text-ink hover:underline">{{ __('Cancel') }}</a>
                            </div>
                        </form>
                    @else
                        <article class="flex flex-col gap-4 rounded-3xl border border-ink/10 bg-card p-6 text-ink shadow-sm transition hover:shadow-md">
                            <div class="flex items-center gap-2">
                                <span class="me-auto h-8 w-8 text-accent/40">{!! $quoteMark !!}</span>

                                {{-- Only the owner sees edit / delete (FR-06, FR-07) --}}
                                @can('update', $quote)
                                    <a href="{{ route('home', [...$state, 'page' => $quotes->currentPage(), 'edit' => $quote->id]) }}" title="{{ __('Edit') }}" aria-label="{{ __('Edit') }}" class="icon-btn px-0">{!! $pen !!}</a>
                                    {{-- asks "are you sure?" first (see #delete-dialog) --}}
                                    <button type="button" data-delete="{{ route('quotes.destroy', $quote) }}" title="{{ __('Delete') }}" aria-label="{{ __('Delete') }}" class="icon-btn px-0 hover:bg-red-500/10 hover:text-red-600">{!! $trash !!}</button>
                                @endcan

                                {{-- Report a bad quote (FR-15), not on my own quotes --}}
                                @auth
                                    @if (auth()->id() !== $quote->user_id)
                                        <button type="button" data-report="{{ route('quotes.report', $quote) }}" title="{{ __('Report') }}" aria-label="{{ __('Report') }}"
                                            class="tap grid h-9 w-9 place-items-center rounded-full text-ink/70 transition hover:bg-red-500/10 hover:text-red-600"><span class="h-4 w-4">{!! $flag !!}</span></button>
                                    @endif
                                @endauth

                                {{-- copy the words, or save them as a picture for stories --}}
                                @php $small = 'tap grid h-9 w-9 place-items-center rounded-full text-ink/70 transition hover:bg-accent/10 hover:text-accent'; @endphp
                                <button type="button" data-copy="{{ $quote->content }}" data-author="{{ $quote->author }}" title="{{ __('Copy quote') }}" aria-label="{{ __('Copy quote') }}" class="{{ $small }}"><span class="h-4 w-4">{!! $copy !!}</span></button>
                                <button type="button" data-image data-text="{{ $quote->content }}" data-author="{{ $quote->author }}" data-category="{{ __($quote->category->name) }}"
                                    title="{{ __('Save as image') }}" aria-label="{{ __('Save as image') }}" class="{{ $small }}"><x-icon name="image" class="h-4 w-4" /></button>

                                <span class="rounded-full bg-accent/10 px-3 py-1 text-xs font-bold text-accent">{{ __($quote->category->name) }}</span>
                            </div>

                            <div class="flex flex-1 flex-col gap-2">
                                <a href="{{ route('quotes.show', $quote) }}" class="font-quote text-xl leading-relaxed hover:text-accent" dir="auto">{{ $quote->content }}</a>
                                @if ($quote->author)
                                    <p class="text-sm font-bold text-sand" dir="auto">— {{ $quote->author }}</p>
                                @endif
                            </div>

                            <div class="flex items-center gap-2 border-t border-ink/5 pt-4">
                                {{-- the poster's name opens their page --}}
                                <a data-soft data-to-quotes href="{{ route('home', ['by' => $quote->user_id]) }}" title="{{ __('Open :name\'s page', ['name' => $quote->user->name]) }}"
                                    class="group me-auto flex min-w-0 items-center gap-2">
                                    <x-avatar :user="$quote->user" />
                                    <span class="flex min-w-0 flex-col leading-tight">
                                        <span class="truncate text-sm font-bold group-hover:text-accent group-hover:underline">{{ auth()->id() === $quote->user_id ? __('You') : $quote->user->name }}</span>
                                        <span class="text-xs text-ink/70">{{ $quote->created_at->format('Y-m-d') }}</span>
                                        <span class="hidden text-xs text-ink/70 sm:block">{{ $quote->created_at->hijri() }}</span>
                                    </span>
                                </a>

                                {{-- the buttons stay together on one line, even on small phones --}}
                                <div class="flex shrink-0 items-center gap-0.5">
                                {{-- Like: everyone sees the count, logged-in users can like (FR-08) --}}
                                @auth
                                    @php $liked = in_array($quote->id, $likedIds); @endphp
                                    <form data-soft method="POST" action="{{ route('quotes.like', $quote) }}">
                                        @csrf
                                        <button type="submit" title="{{ $liked ? __('Unlike') : __('Like') }}" aria-pressed="{{ $liked ? 'true' : 'false' }}"
                                            class="icon-btn {{ $liked ? 'text-red-600' : '' }}">{!! $liked ? $heart : $heartLine !!}{{ $quote->likes_count }}</button>
                                    </form>

                                    {{-- Save to my favorites (FR-09) --}}
                                    @php $saved = in_array($quote->id, $favIds); @endphp
                                    <form data-soft method="POST" action="{{ route('quotes.favorite', $quote) }}">
                                        @csrf
                                        <button type="submit" title="{{ $saved ? __('Unsave') : __('Save') }}" aria-label="{{ $saved ? __('Unsave') : __('Save') }}" aria-pressed="{{ $saved ? 'true' : 'false' }}"
                                            class="icon-btn px-0 {{ $saved ? 'text-accent' : '' }}">{!! $saved ? $bookmarkFull : $bookmark !!}</button>
                                    </form>

                                @else
                                    <a href="{{ route('login') }}" title="{{ __('Like') }}" class="icon-btn">{!! $heartLine !!}{{ $quote->likes_count }}</a>
                                @endauth

                                {{-- comments are on the quote's own page (FR-22) --}}
                                <a href="{{ route('quotes.show', $quote) }}#comments" title="{{ __('Comments') }}" aria-label="{{ __('Comments') }}" class="icon-btn"><x-icon name="comment" />{{ $quote->comments_count }}</a>
                            </div>
                            </div>
                        </article>
                    @endif
                @empty
                    <p class="col-span-full rounded-3xl border border-dashed border-ink/20 p-10 text-center text-ink/70">
                        {{ match ($view) { 'favs' => __('No favorites yet. Tap the bookmark on a quote to save it here.'), 'likes' => __('You haven\'t liked any quotes yet. Tap the heart on a quote you love.'), 'mine' => __('You haven\'t shared any quotes yet.'), default => $poster ? __(':name hasn\'t shared any quotes yet.', ['name' => $poster->name]) : __('No quotes found.') } }}
                    </p>
                @endforelse
            </div>

            {{-- pages: 12 quotes per page so the page stays fast --}}
            @if ($quotes->hasPages())
                @php $pageBtn = 'grid h-10 min-w-10 place-items-center rounded-full border px-3 font-bold transition'; @endphp
                <nav class="flex flex-wrap items-center justify-center gap-2 pt-10" aria-label="{{ __('Pages') }}">
                    @if ($quotes->onFirstPage())
                        <span class="{{ $pageBtn }} border-ink/10 text-ink/30">{{ __('Previous') }}</span>
                    @else
                        <a data-soft data-to-quotes href="{{ $quotes->previousPageUrl() }}" class="{{ $pageBtn }} border-ink/15 bg-card hover:border-accent hover:text-accent">{{ __('Previous') }}</a>
                    @endif
                    @foreach ($quotes->getUrlRange(max(1, $quotes->currentPage() - 2), min($quotes->lastPage(), $quotes->currentPage() + 2)) as $number => $url)
                        <a data-soft data-to-quotes href="{{ $url }}" @if ($number === $quotes->currentPage()) aria-current="page" @endif
                            class="{{ $pageBtn }} {{ $number === $quotes->currentPage() ? 'border-accent bg-accent text-on-accent' : 'border-ink/15 bg-card hover:border-accent hover:text-accent' }}">{{ $number }}</a>
                    @endforeach
                    @if ($quotes->hasMorePages())
                        <a data-soft data-to-quotes href="{{ $quotes->nextPageUrl() }}" class="{{ $pageBtn }} border-ink/15 bg-card hover:border-accent hover:text-accent">{{ __('Next') }}</a>
                    @else
                        <span class="{{ $pageBtn }} border-ink/10 text-ink/30">{{ __('Next') }}</span>
                    @endif
                </nav>
                <p class="text-center text-sm text-ink/70">{{ __('Page :current of :last', ['current' => $quotes->currentPage(), 'last' => $quotes->lastPage()]) }}</p>
            @endif
        </section>
    </div>


    @auth
        {{-- Floating "Share a quote" button: a round "+" that opens up to show its words on the first screen and on hover --}}
        <button type="button" popovertarget="quote-popup" aria-label="{{ __('Share a quote') }}"
            class="share-fab float-hide fixed bottom-8 end-8 z-40 flex h-14 min-w-14 items-center justify-center rounded-full bg-accent px-4 text-lg font-bold text-on-accent shadow-xl shadow-accent/30 transition hover:-translate-y-1 hover:brightness-110 focus:outline-none focus-visible:ring-4 focus-visible:ring-accent/30">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-6 w-6 shrink-0" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span class="fab-label">{{ __('Share a quote') }}</span>
        </button>

        {{-- Pop-up form (FR-05) --}}
        <div id="quote-popup" popover
            class="m-auto w-[90%] max-w-lg rounded-3xl bg-card p-0 shadow-2xl backdrop:bg-black/40 backdrop:backdrop-blur-sm">

            <form method="POST" action="{{ route('quotes.store') }}" class="space-y-4 p-6">
                @csrf
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-ink">{{ __('Share a quote') }}</h2>
                    <button type="button" popovertarget="quote-popup" popovertargetaction="hide" aria-label="Close"
                        class="rounded-full p-1 text-ink/70 hover:bg-ink/10 hover:text-ink">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="h-6 w-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <select name="category_id" required class="input">
                    <option value="" disabled @selected(! old('category_id'))>{{ __('Choose a category') }}</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>{{ __($cat->name) }}</option>
                    @endforeach
                </select>

                <div>
                    <textarea id="quote-body" dir="auto" name="content" rows="5" required minlength="3" maxlength="155" autofocus
                        placeholder="{{ __('Write your quote…') }}" class="input peer font-quote resize-none text-lg user-invalid:border-red-500">{{ old('content') }}</textarea>
                    <p class="mt-1 flex justify-between text-xs text-ink/70 peer-user-invalid:text-red-600">
                        <span>{{ __('3–155 characters.') }}</span>
                        <span><span id="quote-count">0</span>/155</span>
                    </p>
                </div>

                <input type="text" name="author" maxlength="80" value="{{ old('author') }}" dir="auto"
                    placeholder="{{ __('Who said it? (optional)') }}" class="input">

                <button type="submit" class="btn-primary w-full">{{ __('Send') }}</button>
            </form>
        </div>

        {{-- live character counter for the new quote --}}
        <script>
            const quoteBody = document.getElementById('quote-body');
            quoteBody.addEventListener('input', () => {
                document.getElementById('quote-count').textContent = quoteBody.value.length;
            });
        </script>
    @else
        {{-- Guests are asked to log in before sharing (US-01) --}}
        <a href="{{ route('login') }}" aria-label="{{ __('Log in and share your quote with us') }}"
            class="share-fab float-hide fixed bottom-8 end-8 z-40 flex h-14 min-w-14 items-center justify-center rounded-full bg-accent px-4 font-bold text-on-accent shadow-xl shadow-accent/30 transition hover:-translate-y-1 hover:brightness-110">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-6 w-6 shrink-0" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span class="fab-label">{{ __('Log in and share your quote with us') }}</span>
        </a>
    @endauth

    {{-- show each part with a soft fade when it scrolls into view --}}
    <script>
        // children of a .stagger box come in one after the other
        document.querySelectorAll('.stagger').forEach((box) => {
            [...box.children].forEach((child, i) => child.style.setProperty('--i', i));
        });
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    el.classList.add('shown');
                    observer.unobserve(el);
                    // after the entrance, hover effects work without the delay
                    setTimeout(() => el.classList.add('settled'), 900 + el.children.length * 90);
                }
            });
        }, { threshold: 0.12 });
        document.querySelectorAll('.reveal, .stagger').forEach((el) => observer.observe(el));

        // opening picture: the photo moves slower than the page and the quote fades away (parallax)
        const heroBg = document.getElementById('hero-bg');
        const heroContent = document.getElementById('hero-content');
        if (heroBg && !matchMedia('(prefers-reduced-motion: reduce)').matches) {
            let ticking = false;
            const move = () => {
                const y = Math.min(window.scrollY, window.innerHeight);
                heroBg.style.transform = `translateY(${y * 0.35}px) scale(${1 + y / 4000})`;
                heroContent.style.transform = `translateY(${y * 0.25}px)`;
                heroContent.style.opacity = 1 - y / (window.innerHeight * 0.75);
                ticking = false;
            };
            window.addEventListener('scroll', () => {
                if (!ticking) { requestAnimationFrame(move); ticking = true; }
            }, { passive: true });
        }
    </script>

    {{-- "Join" section: move to the next quote card every 5.5 seconds (slow and calm) --}}
    <script>
        (() => {
            const cards = [...document.querySelectorAll('[data-fan-card]')];
            if (cards.length < 2 || matchMedia('(prefers-reduced-motion: reduce)').matches) return;
            const spots = ['pos-front', 'pos-right', 'pos-left'];
            let first = 0;
            setInterval(() => {
                if (document.hidden) return;
                first = (first + 1) % cards.length;
                cards.forEach((card, i) => {
                    const place = (i - first + cards.length) % cards.length;
                    card.classList.remove('pos-front', 'pos-right', 'pos-left', 'pos-hidden');
                    card.classList.add(spots[place] ?? 'pos-hidden');
                });
            }, 5500);
        })();
    </script>

    @auth
        <x-report-dialog soft :title="__('Report this quote')" :question="__('Why is this quote not suitable?')" />
        {{-- "are you sure?" before deleting a quote --}}
        <dialog id="delete-dialog" class="auth-card glass m-auto w-[90%] max-w-sm rounded-3xl p-0 text-ink shadow-2xl backdrop:bg-black/40">
            <form data-soft method="POST" action="" class="flex flex-col gap-4 p-7 text-center">
                @csrf
                @method('DELETE')
                <span class="mx-auto grid h-14 w-14 place-items-center rounded-full bg-red-500/10 text-red-600"><span class="h-7 w-7">{!! $trash !!}</span></span>
                <h2 class="text-xl font-extrabold">{{ __('Delete this quote?') }}</h2>
                <p class="text-sm text-ink/70">{{ __('It will be removed for everyone and cannot be brought back.') }}</p>
                <div class="flex gap-2">
                    <button type="submit" class="h-11 flex-1 rounded-xl bg-red-600 font-bold text-white transition hover:bg-red-700">{{ __('Delete') }}</button>
                    <button type="button" data-delete-cancel class="h-11 flex-1 rounded-xl border border-ink/15 font-bold transition hover:bg-ink/5" autofocus>{{ __('Cancel') }}</button>
                </div>
            </form>
        </dialog>
        <script>
            (() => {
                const del = document.getElementById('delete-dialog');
                document.addEventListener('click', (e) => {
                    const button = e.target.closest('[data-delete]');
                    if (button) {
                        del.querySelector('form').action = button.dataset.delete;
                        del.showModal();
                    }
                    if (e.target.closest('[data-delete-cancel]')) del.close();
                });
            })();
        </script>
    @endauth

    {{-- Filters, tabs, sort, search, like, save and report update the list in place,
         so the page stays where you are instead of jumping to the top --}}
    <script>
        (() => {
            const parts = ['search-form', 'categories', 'trending-slot', 'quotes', 'toast'];

            const swap = async (url, options = {}) => {
                document.getElementById('quotes')?.classList.add('opacity-60');
                try {
                    const response = await fetch(url, options);
                    const page = new DOMParser().parseFromString(await response.text(), 'text/html');
                    parts.forEach((id) => {
                        const now = document.getElementById(id);
                        const next = page.getElementById(id);
                        if (id === 'toast') {
                            now?.remove();
                            if (next) document.body.append(next), window.showToast?.(next);
                            return;
                        }
                        if (!now || !next) return;
                        // new parts are already "shown", no entrance animation again
                        [next, ...next.querySelectorAll('.reveal, .stagger')].forEach((el) => {
                            if (el.matches('.reveal, .stagger')) el.classList.add('shown', 'settled');
                        });
                        now.replaceWith(next);
                    });
                    history.replaceState(null, '', response.url);
                } catch {
                    location.href = url;
                }
            };

            document.addEventListener('click', (e) => {
                const link = e.target.closest('a[data-soft]');
                if (!link || e.ctrlKey || e.metaKey) return;
                e.preventDefault();
                swap(link.href).then(() => {
                    // a new page of quotes starts at the top of the list
                    if (link.hasAttribute('data-to-quotes')) document.getElementById('quotes')?.scrollIntoView({ behavior: 'smooth' });
                });
            });

            document.addEventListener('submit', (e) => {
                const form = e.target;
                if (!form.matches('form[data-soft]')) return;
                e.preventDefault();
                form.closest('dialog')?.close();
                const data = new FormData(form);
                if (form.method.toLowerCase() === 'get') {
                    swap(form.action + '?' + new URLSearchParams(data));
                } else {
                    swap(form.action, { method: 'POST', body: data, headers: { Accept: 'text/html' } });
                }
            });
        })();
    </script>
@endsection
