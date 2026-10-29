<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="{{ session('theme') === 'dark' ? 'dark' : '' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Iqtabis | اقتبس')</title>
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <meta name="theme-color" content="#2F6A9A">
    {{-- link preview when the site is shared on WhatsApp, X, Telegram… --}}
    <meta name="description" content="{{ __('Iqtabis is a home for quotes: find words for every moment, browse by category, like and save the ones that touch you, and share your own.') }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Iqtabis | اقتبس">
    {{-- a page can set its own preview text, like a single quote's page --}}
    <meta property="og:title" content="@yield('og_title', (app()->getLocale() === 'ar' ? 'اقتبس' : 'Iqtabis') . ' | ' . __('A quote for every moment'))">
    <meta property="og:description" content="@yield('og_description', __('Iqtabis is a home for quotes: find words for every moment, browse by category, like and save the ones that touch you, and share your own.'))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('images/og.jpg') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="{{ app()->getLocale() === 'ar' ? 'ar_SA' : 'en_US' }}">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&family=Amiri:wght@400;700&family=Aref+Ruqaa:wght@400;700&family=Playfair+Display:ital,wght@1,600;1,700&display=swap" rel="stylesheet">
    {{-- lets the CSS know JavaScript is on (for the scroll animations) --}}
    <script>document.documentElement.classList.add('js')</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="min-h-screen bg-page text-ink transition-colors duration-300"
>
<x-navbar :page="$page ?? ''" />


    <main>
        @yield('content')
    </main>

    <x-site-footer />

    {{-- "Back to top": a round button that shows up once you scroll past the first screen --}}
    <button type="button" id="to-top" title="{{ __('Back to top') }}" aria-label="{{ __('Back to top') }}"
        class="float-lift fixed bottom-5 start-4 z-40 grid h-11 w-11 sm:bottom-8 sm:h-12 sm:w-12 translate-y-4 place-items-center rounded-full bg-sand text-on-accent opacity-0 shadow-lg shadow-sand/30 transition duration-300 pointer-events-none hover:-translate-y-0.5 hover:brightness-110 sm:start-8">
        <x-icon name="arrow" class="h-5 w-5 -rotate-90" />
    </button>
    <script>
        const toTop = document.getElementById('to-top');
        const toggleToTop = () => {
            const show = window.scrollY > window.innerHeight * 0.8;
            toTop.classList.toggle('opacity-0', !show);
            toTop.classList.toggle('translate-y-4', !show);
            toTop.classList.toggle('pointer-events-none', !show);
            // the share button shows its words only near the top, then shrinks to a small "+"
            document.querySelectorAll('.share-fab').forEach((fab) => fab.classList.toggle('is-open', window.scrollY < window.innerHeight * 0.5));
        };
        window.addEventListener('scroll', toggleToTop, { passive: true });
        toggleToTop();
        toTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

        // at the footer the floating buttons get out of the way: "share" fades out, "back to top" steps up above the last line
        const footerBar = document.getElementById('footer-bar');
        new IntersectionObserver(([entry]) => {
            document.body.style.setProperty('--footer-lift', footerBar.offsetHeight + 'px');
            document.body.classList.toggle('footer-in-view', entry.isIntersecting);
        }).observe(document.querySelector('footer'));
    </script>

    {{-- a short message after an action (shared, updated, deleted, logged in…) that hides by itself --}}
    @if (session('status'))
        <div id="toast" role="status"
            class="toast fixed inset-x-0 bottom-28 z-[60] mx-auto w-fit max-w-[90%] rounded-full bg-ink px-6 py-3 text-center font-bold text-page shadow-2xl">
            {{ session('status') }}
        </div>
    @endif
    <script>
        window.showToast = (toast) => {
            requestAnimationFrame(() => toast.classList.add('toast-in'));
            setTimeout(() => toast.classList.remove('toast-in'), 3500);
            setTimeout(() => toast.remove(), 4000);
        };
        const firstToast = document.getElementById('toast');
        if (firstToast) window.showToast(firstToast);
    </script>

    @guest
        <x-auth-modal />
    @else
        <x-account-modal />
    @endguest

    <x-quote-tools />
    <x-password-toggle />
    <x-form-helpers />

</body>

</html>
