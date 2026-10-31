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

    {{-- Opening screen: an old manuscript on a desk that unrolls, then the quote of the day is written on it --}}
    @if ($browsing && $quoteOfDay)
        <section id="hero" class="desk-hero relative -mt-[74px] flex min-h-svh items-center justify-center overflow-hidden bg-bar text-white">
            {{-- an old wooden desk with a warm lamp light on it (all CSS, no pictures) --}}
            <div id="hero-bg" class="wood absolute inset-0" aria-hidden="true">
                <div class="wood-lamp absolute inset-0"></div>
            </div>

            {{-- an old manuscript: it unrolls from top to bottom, then the words are written on it in ink with a quill --}}
            <div class="scroll-scene relative mx-auto w-full px-5 pb-24 pt-28">
                <div class="scroll">
                    <span class="scroll-rod scroll-rod-top" aria-hidden="true"></span>
                    <div class="scroll-sheet" dir="rtl">
                        <div class="scroll-frame">
                            <p class="font-brand scroll-brand" data-ink>اقتبس</p>
                            {{-- after the slogan the quill goes back to the ink pot for more ink (data-ink-dip) --}}
                            <p class="{{ app()->getLocale() === 'ar' ? 'font-brand text-xl md:text-2xl' : 'font-display text-lg italic' }} opacity-80" data-ink data-ink-dip>{{ __('A quote for every moment') }}</p>
                            <span class="scroll-ornament" aria-hidden="true"></span>
                            <span class="scroll-label" data-ink>{{ __('QUOTE OF THE DAY') }}</span>
                            <blockquote data-ink-words class="font-quote text-2xl leading-relaxed md:text-3xl md:leading-relaxed" dir="auto">{{ $quoteOfDay->content }}</blockquote>
                            <p class="scroll-who" data-ink>— {{ $quoteOfDay->author ?: $quoteOfDay->user->name }} · {{ __($quoteOfDay->category->name) }}</p>
                            <p class="text-xs font-bold opacity-70" data-ink>{{ now()->hijri(true) }} · {{ now()->translatedFormat('j F Y') }}</p>
                            <div class="scroll-actions flex items-center gap-2">
                                <span class="scroll-chip"><span class="h-4 w-4 text-rose-600">{!! $heart !!}</span>{{ $quoteOfDay->likes_count }}</span>
                                <button type="button" data-copy="{{ $quoteOfDay->content }}" data-author="{{ $quoteOfDay->author }}" title="{{ __('Copy quote') }}" aria-label="{{ __('Copy quote') }}"
                                    class="scroll-chip w-10 justify-center px-0"><span class="h-[18px] w-[18px]">{!! $copy !!}</span></button>
                            </div>
                        </div>
                    </div>
                    <span class="scroll-rod scroll-rod-bottom" aria-hidden="true"></span>

                    {{-- an old brass ink pot on a little tray, its lid open, on the desk next to the manuscript (under it on phones) --}}
                    <svg class="inkwell" viewBox="0 0 64 64" aria-hidden="true">
                        <defs>
                            <radialGradient id="brass" cx=".35" cy=".3" r=".8">
                                <stop offset="0" stop-color="#F1D58A"/><stop offset=".45" stop-color="#B8862E"/><stop offset="1" stop-color="#5A3A0C"/>
                            </radialGradient>
                        </defs>
                        <ellipse cx="32" cy="60" rx="29" ry="3.5" fill="#000" opacity=".45"/>
                        <ellipse cx="32" cy="55.5" rx="28" ry="6" fill="#6E4A14"/>
                        <ellipse cx="32" cy="54.5" rx="25" ry="4.6" fill="url(#brass)"/>
                        <path d="M14 53 C14 35 22 25 32 25 C42 25 50 35 50 53 Z" fill="url(#brass)" stroke="#5A3A0C" stroke-width=".6"/>
                        <path d="M15.5 44 Q32 49 48.5 44" stroke="#5A3A0C" stroke-width=".8" fill="none"/>
                        <path d="M17 47.5 Q32 52 47 47.5" stroke="#5A3A0C" stroke-width=".5" fill="none"/>
                        @for ($i = 0; $i < 7; $i++)
                            <circle cx="{{ 19 + $i * 4.3 }}" cy="{{ 45.6 + ($i === 0 || $i === 6 ? -.6 : ($i === 3 ? 1.5 : 1)) }}" r=".7" fill="#5A3A0C"/>
                        @endfor
                        <path d="M19 44 C19 34 24 29 28 28" stroke="#fff" stroke-width="1.6" stroke-linecap="round" fill="none" opacity=".3"/>
                        <rect x="24" y="16" width="16" height="10" rx="2" fill="url(#brass)" stroke="#5A3A0C" stroke-width=".5"/>
                        <ellipse cx="32" cy="16" rx="9" ry="2.6" fill="#E3C46E"/>
                        <ellipse cx="32" cy="16.3" rx="6.5" ry="1.8" fill="#0B0705"/>
                        <path d="M41 15.5 C43 6 51 3 57 7 C55 12 48 15.5 41 15.5 Z" fill="url(#brass)" stroke="#5A3A0C" stroke-width=".5"/>
                        <circle cx="55.5" cy="5.5" r="1.6" fill="#E3C46E" stroke="#5A3A0C" stroke-width=".4"/>
                    </svg>
                    {{-- the quill: it waits in the ink pot, writes, dips again, and goes back (see the script below) --}}
                    <svg class="quill" viewBox="0 0 64 64" aria-hidden="true">
                        <defs>
                            <linearGradient id="quill-vane" x1="0" y1="1" x2="1" y2="0">
                                <stop offset="0" stop-color="#9C7A55"/><stop offset=".6" stop-color="#E9D9BC"/><stop offset="1" stop-color="#F7EEDC"/>
                            </linearGradient>
                        </defs>
                        <path d="M9 55 C14 40 30 18 61 3 C57 20 45 38 19 52 Z" fill="url(#quill-vane)" stroke="#5C3A1E" stroke-width=".8"/>
                        @for ($i = 0; $i < 7; $i++)
                            @php $t = .25 + $i * .1; $x = 5 + 45 * $t; $y = 59 - 47 * $t; @endphp
                            <path d="M{{ $x }} {{ $y }} l{{ 6 + $i }} {{ 3 - $i * .6 }}" stroke="#7A5634" stroke-width=".5" fill="none"/>
                            <path d="M{{ $x }} {{ $y }} l{{ -1 - $i * .4 }} {{ -5 - $i }}" stroke="#7A5634" stroke-width=".5" fill="none"/>
                        @endfor
                        <path d="M5 59 L52 10" stroke="#5C3A1E" stroke-width="1.4" fill="none"/>
                        <path d="M3 61 L8 52 L11 55 Z" fill="#2A1A0C"/>
                    </svg>
                </div>
            </div>

            {{-- scroll down to see the rest --}}
            <a href="#explore" class="scroll-hint absolute bottom-6 flex flex-col items-center gap-1 text-sm text-white/80 transition hover:text-white">
                {{ __('Scroll to explore') }}
                <svg class="h-6 w-6 animate-bounce" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
            </a>
        </section>

        {{-- a slow strip of the authors on the site (colors in app.css; English names in lang/en.json); pressing a name shows their quotes --}}
        @if ($authors->count() >= 3)
            <nav aria-label="{{ __('Authors') }}" class="marquee overflow-hidden py-3">
                <div class="marquee-track flex w-max">
                    @foreach ([false, true] as $repeat)
                        <ul class="flex shrink-0 items-center" @if ($repeat) aria-hidden="true" @endif>
                            @foreach ($authors as $author)
                                <li class="flex items-center">
                                    <a href="{{ route('home', ['q' => $author]) }}#quotes" @if ($repeat) tabindex="-1" @endif
                                        class="whitespace-nowrap px-6 font-bold transition hover:text-copper hover:underline">{{ __($author) }}</a>
                                    <span class="marquee-mark h-4 w-4">{!! $quoteMark !!}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endforeach
                </div>
            </nav>
        @endif
    @endif

    {{-- under the opening video, the page sits on a dark painting instead of the blurred photo --}}
    <x-painting-bg />

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
            <dl id="stats" class="stagger grid w-full grid-cols-2 gap-6 md:grid-cols-4">
                @foreach ($stats as [$number, $label])
                    <div class="flex flex-col items-center gap-1">
                        <dt class="order-2 text-sm text-ink/70">{{ $label }}</dt>
                        <dd class="order-1 text-3xl font-extrabold tabular-nums text-accent" dir="ltr" data-count="{{ $number }}">{{ $number }}</dd>
                    </div>
                @endforeach
            </dl>

            {{-- what the site offers, on one coloured band --}}
            <div class="reveal w-full rounded-3xl bg-accent px-6 py-6 text-on-accent shadow-lg md:px-10">
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

    {{-- Voices: while the section is pinned, a big sentence shows first and fades on the first scroll,
         then each scroll brings in one more picture, each in its own place so none cover another.
         Pressing a picture shows that person's quotes, like the authors strip. Pictures live in public/images/poets/<slug>.jpg (4:5);
         only people with a real picture show, so the old poets (no real portraits) wait until one is added --}}
    @php
        // who said it => [era or what they were, picture file name]
        $poets = [
            'أحمد شوقي' => ['Modern era', 'shawqi'], 'المتنبي' => ['Abbasid era', 'mutanabbi'],
            'أبو القاسم الشابي' => ['Modern era', 'shabbi'], 'امرؤ القيس' => ['Pre-Islamic era', 'imru-al-qais'],
            'حافظ إبراهيم' => ['Modern era', 'hafez'], 'الإمام الشافعي' => ['Abbasid era', 'shafii'],
            'أبو فراس الحمداني' => ['Abbasid era', 'abu-firas'], 'الخنساء' => ['Early Islamic era', 'khansa'],
            'طرفة بن العبد' => ['Pre-Islamic era', 'tarafa'],
            'William Shakespeare' => ['English playwright', 'shakespeare'], 'Oscar Wilde' => ['Irish writer', 'wilde'],
            'Albert Einstein' => ['Physicist', 'einstein'], 'Terry Pratchett' => ['English novelist', 'pratchett'],
            'نزار قباني' => ['Modern era', 'nizar'], 'محمود درويش' => ['Modern era', 'darwish'],
        ];
        $onSite = $browsing ? \App\Models\Quote::visibleTo(auth()->user())->whereIn('author', array_keys($poets))->distinct()->pluck('author')->all() : [];
        $shown = collect($poets)->only($onSite)->filter(fn ($poet) => file_exists(public_path("images/poets/{$poet[1]}.jpg")))->take(9);
    @endphp
    @if ($shown->count() >= 3)
        <section id="poets" class="relative" style="height: {{ 100 + $shown->count() * 30 }}svh" aria-labelledby="poets-title">
            <div class="sticky top-0 h-svh overflow-hidden">
                <div id="poets-intro" class="absolute inset-0 flex flex-col items-center justify-center gap-4 px-6 text-center">
                    <p class="text-sm font-bold tracking-wide text-copper">{{ __('From the voices') }}</p>
                    <h2 id="poets-title" class="{{ app()->getLocale() === 'ar' ? 'font-brand text-6xl md:text-8xl' : 'font-display text-5xl italic md:text-7xl' }} max-w-3xl leading-tight text-ink">{{ __('Words that never grow old') }}</h2>
                </div>
                <div class="absolute inset-0 flex items-center justify-center px-4 pt-16">
                    <div class="flex max-w-5xl flex-wrap items-start justify-center gap-x-3 gap-y-4 md:gap-x-6 md:gap-y-6">
                        @foreach ($shown as $poet => [$era, $slug])
                            {{-- every other picture sits a little lower, so the row looks laid by hand --}}
                            <a href="{{ route('home', ['q' => $poet]) }}#quotes" aria-label="{{ __($poet) }}"
                                class="poet-card group flex w-[29%] flex-col gap-1.5 sm:w-36 md:w-40 {{ $loop->even ? 'md:mt-8' : '' }}">
                                <img src="{{ asset("images/poets/$slug.jpg") }}" alt="" loading="lazy"
                                    class="aspect-[4/5] w-full rounded-xl object-cover shadow-lg grayscale transition duration-500 group-hover:grayscale-0 group-hover:shadow-xl">
                                <span class="text-[11px] font-bold leading-snug text-ink md:text-sm">{{ __($poet) }}</span>
                                <span class="-mt-1 text-[10px] text-sand md:text-xs">{{ __($era) }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- Join the community (guests only): text on one side, a fan of real quote cards on the other --}}
    @if (auth()->guest() && $browsing && $trending->isNotEmpty())
        <section id="join" class="reveal mx-auto max-w-6xl px-6 pt-16">
            <div class="grid items-center gap-10 rounded-[2rem] border border-ink/10 bg-card p-8 shadow-sm md:grid-cols-2 md:p-12">
                <div class="flex flex-col items-start gap-5">
                    <h2 class="{{ app()->getLocale() === 'ar' ? 'font-quote text-4xl sm:text-5xl lg:text-6xl leading-tight' : 'font-display text-4xl italic sm:text-5xl leading-tight' }} font-bold text-ink">{{ __('Join the Iqtabis community') }}</h2>
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
        <div class="reveal mx-auto flex max-w-3xl flex-col items-center gap-5 px-6 pb-10 pt-16 text-center">
            {{-- a sister line to the slogan, so it isn't repeated: Ruqaa script in Arabic, Playfair italic in English --}}
            <h1 class="{{ app()->getLocale() === 'ar' ? 'font-brand text-4xl sm:text-5xl md:text-6xl leading-snug' : 'font-display text-4xl italic sm:text-5xl md:text-6xl leading-tight' }} font-bold">{{ __('Find a quote that matches your moment') }}</h1>

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
            class="reveal grid grid-cols-2 gap-3 rounded-3xl border border-ink/10 bg-card p-4 shadow-sm sm:grid-cols-3 lg:grid-cols-9">
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
                <div class="reveal">
                    <h2 class="text-3xl font-extrabold text-ink">{{ __('Trending') }}</h2>
                    <p class="text-ink/70">
                        {{ $filter ? __('The most liked :category quotes.', ['category' => __($filter)]) : __('The most liked quotes right now.') }}
                    </p>
                </div>
                <div class="stagger grid gap-5 md:grid-cols-3">
                    @foreach ($trending as $quote)
                        {{-- same cut-corner card as the quotes below; the whole card opens the quote's page, with its comments --}}
                        <div class="notch-wrap">
                        <div class="notch-shape">
                        <a href="{{ route('quotes.show', $quote) }}" class="notch flex flex-col gap-4 rounded-md bg-card p-6 text-ink focus:outline-none focus-visible:bg-accent/5">
                            <div class="flex items-center gap-3">
                                <span class="me-auto flex h-9 w-9 items-center justify-center rounded-full bg-accent font-extrabold text-on-accent">{{ $loop->iteration }}</span>
                                <span class="flex items-center gap-1.5 text-sm font-bold text-rose-500"><span class="h-4 w-4">{!! $heart !!}</span>{{ $quote->likes_count }}</span>
                            </div>
                            <p class="font-quote flex-1 text-xl leading-relaxed" dir="auto">{{ $quote->content }}</p>
                        </a>
                        </div>
                        <p class="notch-label">
                            <span class="notch-who" dir="auto">{{ $quote->author ?: $quote->user->name }}</span>
                        </p>
                        <span class="notch-cat">{{ __($quote->category->name) }}</span>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
        </div>

        {{-- All quotes --}}
        <section id="quotes" class="flex scroll-mt-28 flex-col gap-5">
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

            {{-- searching, filtering or someone's page (e.g. after pressing a name in the strip or a picture): a clear way back to the full home page --}}
            @unless ($browsing)
                <a href="{{ route('home') }}" class="inline-flex w-fit items-center gap-2 rounded-full border border-ink/15 bg-card px-4 py-2 text-sm font-bold text-ink shadow-sm transition hover:border-accent hover:text-accent">
                    <svg class="h-4 w-4 ltr:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    {{ __('Back to home') }}
                </a>
            @endunless

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

            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($quotes as $quote)
                    @if ($quote->id === $editingId && auth()->user()?->can('update', $quote))
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
                        {{-- the card has a cut corner at the bottom, the author and category sit in it --}}
                        <div class="notch-wrap reveal" style="--i: {{ $loop->index % 3 }}">
                        <div class="notch-shape">
                        <article class="notch flex flex-col gap-4 rounded-md bg-card p-6 text-ink">
                            <div class="card-tools flex items-center gap-2">
                                <span class="me-auto h-8 w-8 text-accent/40">{!! $quoteMark !!}</span>

                                {{-- Only the owner sees edit / delete (FR-06, FR-07); edit closes after 10 minutes, delete stays --}}
                                @can('update', $quote)
                                    <a href="{{ route('home', [...$state, 'page' => $quotes->currentPage(), 'edit' => $quote->id]) }}" title="{{ __('Edit') }}" aria-label="{{ __('Edit') }}" class="icon-btn px-0">{!! $pen !!}</a>
                                @endcan
                                @if (auth()->id() === $quote->user_id)
                                    {{-- asks "are you sure?" first (see #delete-dialog) --}}
                                    <button type="button" data-delete="{{ route('quotes.destroy', $quote) }}" title="{{ __('Delete') }}" aria-label="{{ __('Delete') }}" class="icon-btn px-0 hover:bg-red-500/10 hover:text-red-600">{!! $trash !!}</button>
                                @endif

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

                            </div>

                            <div class="flex flex-1 flex-col gap-2">
                                <a href="{{ route('quotes.show', $quote) }}" class="font-quote text-xl leading-relaxed hover:text-accent" dir="auto">{{ $quote->content }}</a>
                            </div>

                            <div class="flex items-center gap-2 pt-2">
                                {{-- the poster's name opens their page --}}
                                <a data-soft data-to-quotes href="{{ route('home', ['by' => $quote->user_id]) }}" title="{{ __('Open :name\'s page', ['name' => $quote->user->name]) }}"
                                    class="group me-auto flex min-w-0 items-center gap-2">
                                    <x-avatar :user="$quote->user" />
                                    <span class="flex min-w-0 flex-col leading-tight">
                                        <span class="truncate text-sm font-bold group-hover:text-accent group-hover:underline">{{ auth()->id() === $quote->user_id ? __('You') : $quote->user->name }}</span>
                                        <span class="text-xs text-ink/70">{{ $quote->created_at->format('Y-m-d') }}</span>
                                        <span class="hidden whitespace-nowrap text-[0.7rem] text-ink/70 sm:block">{{ $quote->created_at->hijri() }}</span>
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
                                            class="like-btn icon-btn {{ $liked ? 'text-red-600' : '' }}">{!! $liked ? $heart : $heartLine !!}{{ $quote->likes_count }}</button>
                                    </form>

                                    {{-- Save to my favorites (FR-09) --}}
                                    @php $saved = in_array($quote->id, $favIds); @endphp
                                    <form data-soft method="POST" action="{{ route('quotes.favorite', $quote) }}">
                                        @csrf
                                        <button type="submit" title="{{ $saved ? __('Unsave') : __('Save') }}" aria-label="{{ $saved ? __('Unsave') : __('Save') }}" aria-pressed="{{ $saved ? 'true' : 'false' }}"
                                            class="icon-btn px-0 {{ $saved ? 'text-accent' : '' }}">{!! $saved ? $bookmarkFull : $bookmark !!}</button>
                                    </form>

                                @else
                                    <a href="{{ route('login') }}" title="{{ __('Like') }}" class="like-btn icon-btn">{!! $heartLine !!}{{ $quote->likes_count }}</a>
                                @endauth

                                {{-- comments are on the quote's own page (FR-22) --}}
                                <a href="{{ route('quotes.show', $quote) }}#comments" title="{{ __('Comments') }}" aria-label="{{ __('Comments') }}" class="comment-btn icon-btn"><x-icon name="comment" />{{ $quote->comments_count }}</a>
                            </div>
                            </div>
                        </article>
                        </div>
                        <p class="notch-label">
                            @if ($quote->author)
                                <span class="notch-who" dir="auto">{{ $quote->author }}</span>
                            @endif
                        </p>
                        <span class="notch-cat">{{ __($quote->category->name) }}</span>
                        </div>
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
            class="share-fab float-hide fixed bottom-5 end-4 z-40 hidden sm:flex h-12 min-w-12 sm:bottom-8 sm:end-8 sm:h-14 sm:min-w-14 items-center justify-center rounded-full bg-accent px-4 text-lg font-bold text-on-accent shadow-xl shadow-accent/30 transition hover:-translate-y-1 hover:brightness-110 focus:outline-none focus-visible:ring-4 focus-visible:ring-accent/30">
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
                {{-- the edit window (FR-06), so it's no surprise later --}}
                <p class="-mt-2 text-center text-xs text-ink/60">{{ __('You can edit your quote for :minutes minutes after posting.', ['minutes' => config('moderation.edit_minutes')]) }}</p>
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
            class="share-fab float-hide fixed bottom-5 end-4 z-40 hidden sm:flex h-12 min-w-12 sm:bottom-8 sm:end-8 sm:h-14 sm:min-w-14 items-center justify-center rounded-full bg-accent px-4 font-bold text-on-accent shadow-xl shadow-accent/30 transition hover:-translate-y-1 hover:brightness-110">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-6 w-6 shrink-0" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span class="fab-label">{{ __('Log in and share your quote with us') }}</span>
        </a>
    @endauth

    {{-- Phones: the round "+" opens a small menu, so it's clear what it's for --}}
    <button type="button" popovertarget="fab-menu" aria-label="{{ __('Menu') }}"
        class="fab-phone float-hide fixed bottom-5 end-4 z-40 grid h-13 w-13 place-items-center rounded-full bg-accent text-on-accent shadow-xl shadow-accent/30 transition active:scale-95 sm:hidden">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-6 w-6 transition-transform duration-300" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
    </button>
    <div id="fab-menu" popover class="fab-menu rounded-2xl border border-ink/10 bg-card p-2 text-ink shadow-2xl">
        @auth
            <button type="button" popovertarget="quote-popup" class="fab-item">
                <span class="fab-ico bg-accent text-on-accent"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><path d="M12 4.5v15m7.5-7.5h-15"/></svg></span>
                {{ __('Share a quote') }}
            </button>
        @else
            <a href="{{ route('login') }}" class="fab-item">
                <span class="fab-ico bg-accent text-on-accent"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><path d="M12 4.5v15m7.5-7.5h-15"/></svg></span>
                {{ __('Log in and share your quote with us') }}
            </a>
        @endauth
        <a href="{{ route('quotes.random') }}" class="fab-item">
            <span class="fab-ico bg-sand/15 text-sand"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="4"/><circle cx="8.5" cy="8.5" r="1.2" fill="currentColor"/><circle cx="15.5" cy="15.5" r="1.2" fill="currentColor"/><circle cx="12" cy="12" r="1.2" fill="currentColor"/></svg></span>
            {{ __('Quote me') }}
        </a>
    </div>
    <script>
        // the menu closes once you pick something (the log in pop-up opens over the page, not under the menu)
        document.querySelectorAll('#fab-menu a.fab-item').forEach((item) => item.addEventListener('click', () => document.getElementById('fab-menu').hidePopover()));
    </script>

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

        // voices: while the section is pinned, the sentence shows first and fades on the first scroll,
        // then each scroll brings in one more picture in its own place
        const poets = document.getElementById('poets');
        if (poets) {
            const intro = document.getElementById('poets-intro');
            const cards = [...poets.querySelectorAll('.poet-card')];
            const still = matchMedia('(prefers-reduced-motion: reduce)').matches;
            const clamp = (n) => Math.min(Math.max(n, 0), 1);
            const move = () => {
                const box = poets.getBoundingClientRect();
                const progress = clamp(-box.top / (box.height - innerHeight)); // 0 .. 1
                const gone = clamp((progress - 0.04) / 0.08); // the sentence leaves between 4% and 12%
                intro.style.opacity = 1 - gone;
                if (!still) intro.style.scale = 1 - gone * 0.06;
                cards.forEach((card, i) => {
                    const shown = clamp((progress - (0.14 + (i / cards.length) * 0.76)) / 0.06);
                    card.style.opacity = shown;
                    card.style.pointerEvents = shown ? '' : 'none';
                    if (!still) card.style.translate = `0 ${(1 - shown) * 30}px`;
                });
            };
            move();
            addEventListener('scroll', () => requestAnimationFrame(move), { passive: true });
            addEventListener('resize', move);
        }

        // the site's numbers roll up from 0 like an odometer the first time they come into view:
        // every digit is a column of 0-9 that slides smoothly to its place (the last digit turns the most)
        const stats = document.getElementById('stats');
        if (stats && !matchMedia('(prefers-reduced-motion: reduce)').matches) {
            const numbers = stats.querySelectorAll('[data-count]');
            numbers.forEach((dd) => {
                const digits = String(dd.dataset.count).split('');
                dd.setAttribute('aria-label', dd.dataset.count);
                dd.innerHTML = digits.map((digit, i) => {
                    const turns = Math.max(2 - (digits.length - 1 - i), 0); // 2 extra turns for the last digit, 1 for the one before
                    const strip = Array.from({ length: 30 }, (_, n) => `<span>${n % 10}</span>`).join('');
                    return `<span class="odo" aria-hidden="true"><span class="odo-strip" data-to="${turns * 10 + Number(digit)}">${strip}</span></span>`;
                }).join('');
            });
            new IntersectionObserver(([entry], watcher) => {
                if (!entry.isIntersecting) return;
                watcher.disconnect();
                // wait for the numbers to finish fading in, so the roll is seen from 0
                setTimeout(() => stats.querySelectorAll('.odo-strip').forEach((strip) => {
                    strip.style.transform = `translateY(${-strip.dataset.to * 1.25}em)`;
                }), 800);
            }, { threshold: 0.6 }).observe(stats);
        }

        // the opening manuscript: once it has unrolled, the words are written in ink one after the other, and a quill follows them
        const sheet = document.querySelector('.scroll-sheet');
        if (sheet && !matchMedia('(prefers-reduced-motion: reduce)').matches) {
            // the quote is written word by word (each word keeps its letters joined)
            sheet.querySelectorAll('[data-ink-words]').forEach((quote) => {
                quote.innerHTML = quote.textContent.trim().split(/\s+/)
                    .map((word) => `<span data-ink>${word.replace(/[&<>"]/g, (c) => `&#${c.charCodeAt(0)};`)}</span>`).join(' ');
            });
            const parts = [...sheet.querySelectorAll('[data-ink]')];
            const start = performance.now();
            // slow and calm: one word at a time, with a short lift of the quill between words
            const dip = 2700; // going to the ink pot, dipping, and coming back
            const lift = 250;
            let at = 3800; // the sheet has unrolled and the quill has reached the first word
            const steps = [];
            // after "quote of the day" the rest is written a little quicker
            let quick = false;
            parts.forEach((el) => {
                // the name is short but big, so it gets more time, like writing it carefully
                const time = el.classList.contains('scroll-brand') ? 1500 : quick
                    ? Math.min(Math.max(el.textContent.trim().length * 65, 330), 1300)
                    : Math.min(Math.max(el.textContent.trim().length * 90, 420), 1800);
                if (el.classList.contains('scroll-label')) quick = true;
                el.classList.add('ink');
                el.style.setProperty('--ink-at', `${at}ms`);
                el.style.setProperty('--ink-time', `${time}ms`);
                steps.push({ el, at, time });
                at += time;
                if (el.hasAttribute('data-ink-dip')) {
                    steps.push({ dip: true, at });
                    at += dip;
                } else {
                    at += quick ? lift * 0.7 : lift;
                }
            });
            sheet.style.setProperty('--ink-done', `${at}ms`);

            // the quill: it rests in the ink pot, goes over each word from its start to its end (right to left in Arabic), dips again half way, and goes back
            const scroll = sheet.parentElement;
            const quill = scroll.querySelector('.quill');
            const well = scroll.querySelector('.inkwell');
            document.fonts.ready.then(() => {
                const late = performance.now() - start; // fonts may take a moment, keep in time with the ink
                const box = scroll.getBoundingClientRect();
                const point = (x, y, tilt = 0) => `translate(${x - box.left}px, ${y - box.top - quill.clientHeight}px) rotate(${tilt}deg)`;
                const w = well.getBoundingClientRect();
                const mouth = (down = 0) => point(w.left + w.width * .5, w.top + w.height * (.2 + down));
                // while writing the quill moves across at an even speed (exactly with the ink), but its tip goes up and down
                // and it tilts a little, like a hand drawing the letters; between words it eases
                const frames = [{ at: 0, transform: mouth() }, { at: 2900, transform: mouth() }];
                steps.forEach(({ el, at, time, dip: dipping }) => {
                    if (dipping) {
                        frames.push({ at: at + 900, transform: mouth(-.25) }, { at: at + 1300, transform: mouth(.1) },
                            { at: at + 1700, transform: mouth(-.25) });
                        return;
                    }
                    const r = el.getBoundingClientRect();
                    const rtl = getComputedStyle(el).direction === 'rtl';
                    const [from, to] = rtl ? [r.right, r.left] : [r.left, r.right];
                    const base = r.top + r.height * 0.8;
                    const strokes = Math.max(3, Math.round(time / 180)); // gentle strokes, not one per letter, so it is calm to watch
                    for (let i = 0; i <= strokes; i++) {
                        const t = i / strokes;
                        // up for the tall letters, down to the line, with a little randomness so no two strokes look the same
                        const lift = i === 0 || i === strokes ? 0 : (i % 2 ? .12 + Math.random() * .08 : Math.random() * .04);
                        const tilt = i % 2 ? -2 - Math.random() : 1 + Math.random();
                        frames.push({ at: at + time * t, transform: point(from + (to - from) * t, base - r.height * lift, tilt), easing: i < strokes ? 'ease-in-out' : undefined });
                    }
                });
                frames.push({ at: frames.at(-1).at + 1200, transform: mouth() });
                const total = frames.at(-1).at;
                quill.style.opacity = 1;
                quill.animate(frames.map(({ at, transform, easing = 'ease-in-out' }) => ({ transform, easing, offset: at / total })),
                    { duration: total, delay: -late, fill: 'both' });
            });
        }

        // opening picture: the photo moves slower than the page and the quote fades away (parallax)
        const heroBg = document.getElementById('hero-bg');
        const heroContent = document.getElementById('hero-content');
        // people who turn motion off keep the still picture
        if (matchMedia('(prefers-reduced-motion: reduce)').matches) heroBg?.querySelector('video')?.remove();
        // phones sometimes hold autoplay back: start it again now, and on the first touch
        const heroVideo = heroBg?.querySelector('video');
        if (heroVideo) {
            const play = () => heroVideo.play().catch(() => {});
            play();
            addEventListener('touchstart', play, { once: true, passive: true });
        }
        if (heroBg && !matchMedia('(prefers-reduced-motion: reduce)').matches && matchMedia('(min-width: 640px)').matches) {
            let ticking = false;
            const move = () => {
                const y = Math.min(window.scrollY, window.innerHeight);
                heroBg.style.transform = `translateY(${y * 0.35}px) scale(${1 + y / 4000})`;
                if (heroContent) {
                    heroContent.style.transform = `translateY(${y * 0.25}px)`;
                    heroContent.style.opacity = 1 - y / (window.innerHeight * 0.75);
                }
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

            // going back to the plain home page from a search or a filter loads it whole,
            // so the opening video and the sections above the list come back
            const wholeHome = (url) => {
                const to = new URL(url, location.href);
                return !document.getElementById('hero') && to.pathname === location.pathname && ![...to.searchParams.values()].some(Boolean);
            };

            document.addEventListener('click', (e) => {
                const link = e.target.closest('a[data-soft]');
                if (!link || e.ctrlKey || e.metaKey || wholeHome(link.href)) return;
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
                    const url = form.action + '?' + new URLSearchParams(data);
                    wholeHome(url) ? (location.href = url) : swap(url);
                } else {
                    swap(form.action, { method: 'POST', body: data, headers: { Accept: 'text/html' } });
                }
            });
        })();
    </script>
@endsection
