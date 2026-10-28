@props(['page' => ''])

<nav id="navbar" class="glass sticky top-0 z-50 !border-x-0 !border-t-0">
    <div class="flex items-center gap-2 px-3 py-2 sm:gap-3 sm:px-4 md:px-8">

        {{-- the name: Ruqaa script with the colors flowing, English under it --}}
        <a href="{{ route('home') }}" class="flex shrink-0 flex-col items-start sm:me-4" aria-label="Iqtabis | اقتبس">
            <span class="site-name">اقتبس</span>
            <span class="nav-text text-[9px] font-bold tracking-[0.4em] text-sand sm:text-[11px]">IQTABIS</span>
        </a>

        {{-- my avatar and name sit right next to the site name; pressing them opens "My account" --}}
        @auth
            <button type="button" data-account-open title="{{ __('My account') }}"
                class="nav-text flex shrink-0 items-center gap-2 rounded-full p-0.5 font-bold text-ink transition hover:bg-card/30 sm:py-1 sm:pe-3 sm:ps-1">
                <x-avatar :user="auth()->user()" size="h-9 w-9 text-base sm:h-10 sm:w-10" class="ring-2 ring-white/70" />
                {{-- on phones only the picture shows, to keep the bar on one line --}}
                <span class="hidden sm:inline">{{ auth()->user()->name }}</span>
            </button>
        @endauth

        <div class="ms-auto flex items-center gap-1 sm:gap-2">
            @auth
                {{-- admins get a shortcut to the dashboard (FR-25) --}}
                @can('admin')
                    <a href="{{ route('admin.index') }}" title="{{ __('Admin dashboard') }}" aria-label="{{ __('Admin dashboard') }}" class="bar-btn hidden px-0 sm:inline-flex sm:px-0">
                        <x-icon name="shield" class="h-[18px] w-[18px]" />
                    </a>
                @endcan

                {{-- the bell: who liked or commented on my quotes (FR-23) --}}
                @php $unread = auth()->user()->unreadNotifications()->count(); @endphp
                <button type="button" popovertarget="notifications" title="{{ __('Notifications') }}" aria-label="{{ __('Notifications') }}" class="bar-btn relative px-0 sm:px-0">
                    <x-icon name="bell" class="h-[18px] w-[18px]" />
                    @if ($unread)
                        <span class="absolute -end-1 -top-1 grid h-5 min-w-5 place-items-center rounded-full bg-rose-500 px-1 text-[11px] font-bold text-white">{{ $unread > 9 ? '9+' : $unread }}</span>
                    @endif
                </button>
                <div id="notifications" popover
                    class="glass fixed inset-auto end-3 top-16 m-0 w-[min(22rem,calc(100vw-1.5rem))] rounded-2xl p-0 text-ink shadow-2xl sm:top-20">
                    <div class="flex items-center justify-between border-b border-ink/10 px-4 py-3">
                        <p class="font-extrabold">{{ __('Notifications') }}</p>
                        @if ($unread)
                            <form method="POST" action="{{ route('notifications.read') }}">
                                @csrf
                                <button type="submit" class="text-xs font-bold text-accent hover:underline">{{ __('Mark all as read') }}</button>
                            </form>
                        @endif
                    </div>
                    <div class="max-h-96 overflow-y-auto">
                        @forelse (auth()->user()->notifications()->take(10)->get() as $notification)
                            <a href="{{ route('notifications.show', $notification->id) }}"
                                class="flex gap-3 border-b border-ink/5 px-4 py-3 text-sm transition hover:bg-accent/5 {{ $notification->read_at ? 'opacity-60' : '' }}">
                                <span class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-full {{ $notification->data['kind'] === 'like' ? 'bg-rose-500/10 text-rose-500' : 'bg-accent/10 text-accent' }}">
                                    <x-icon :name="$notification->data['kind'] === 'like' ? 'heart' : 'comment'" class="h-4 w-4" />
                                </span>
                                <span class="flex min-w-0 flex-col gap-0.5">
                                    <span>
                                        <b>{{ $notification->data['from'] }}</b>
                                        {{ $notification->data['kind'] === 'like' ? __('liked your quote') : __('commented on your quote') }}
                                    </span>
                                    <span class="truncate font-quote text-ink/70" dir="auto">«{{ $notification->data['quote'] }}»</span>
                                    <span class="text-xs text-ink/70">{{ $notification->created_at->diffForHumans() }}</span>
                                </span>
                                @unless ($notification->read_at)
                                    <span class="ms-auto mt-2 h-2 w-2 shrink-0 rounded-full bg-accent"></span>
                                @endunless
                            </a>
                        @empty
                            <p class="px-4 py-8 text-center text-sm text-ink/70">{{ __('No notifications yet. When someone likes or comments on your quote, you will see it here.') }}</p>
                        @endforelse
                    </div>
                </div>
            @endauth

            @if (session('theme') === 'dark')
                <a href="{{ route('theme', 'light') }}" title="{{ __('Light mode') }}" aria-label="{{ __('Light mode') }}" class="bar-btn px-0 sm:px-0">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
                </a>
            @else
                <a href="{{ route('theme', 'dark') }}" title="{{ __('Dark mode') }}" aria-label="{{ __('Dark mode') }}" class="bar-btn px-0 sm:px-0">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/></svg>
                </a>
            @endif

            @if (app()->getLocale() === 'ar')
                <a href="{{ route('lang', 'en') }}" class="bar-btn">EN</a>
            @else
                <a href="{{ route('lang', 'ar') }}" class="bar-btn">عربي</a>
            @endif

            @auth
                {{-- logout must be POST with @csrf, a GET link is not safe (FR-04) --}}
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="bar-btn" title="{{ __('Log Out') }}" aria-label="{{ __('Log Out') }}">
                        <svg class="h-[18px] w-[18px] sm:hidden rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
                        <span class="hidden sm:inline">{{ __('Log Out') }}</span>
                    </button>
                </form>
            @else
                @if (($page ?? '') !== 'Log In')
                    <a href="{{ route('login') }}" class="bar-btn">{{ __('Log In') }}</a>
                @endif
                @if (($page ?? '') !== 'Sign Up')
                    {{-- on phones Sign Up is reached from the log in window, so the bar fits one line --}}
                    <a href="{{ route('signup') }}" class="bar-btn hidden sm:inline-flex">{{ __('Sign Up') }}</a>
                @endif
            @endauth

            {{-- logos without a box: white over the picture, dark on the light bar --}}
            <img src="{{ asset('images/logo-clear.png') }}" class="nav-logo ms-1 h-6 w-auto shrink-0 sm:ms-2 sm:h-12" alt="Tuwaiq Academy and university logos">
        </div>
    </div>

    {{-- the email is not confirmed yet: sharing and commenting wait for it (FR-24) --}}
    @auth
        @unless (auth()->user()->hasVerifiedEmail() || request()->routeIs('verification.notice'))
            <div class="bg-sand/90 px-4 py-1.5 text-center text-sm text-white">
                {{ __('Confirm your email to share quotes and comment.') }}
                <a href="{{ route('verification.notice') }}" class="font-bold underline">{{ __('Confirm now') }}</a>
            </div>
        @endunless
    @endauth
</nav>

{{-- see-through navbar while the opening picture is under it --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const nav = document.getElementById('navbar');
        const hero = document.getElementById('hero');
        if (!hero) return;
        // on phones only the photo part of the opening screen is dark, the quote under it sits on the page color
        const phone = matchMedia('(max-width: 639px)');
        const edge = () => phone.matches ? document.getElementById('hero-bg').offsetHeight : hero.offsetHeight;
        const update = () => nav.classList.toggle('on-hero', window.scrollY < edge() - nav.offsetHeight);
        update();
        window.addEventListener('scroll', update, { passive: true });
    });
</script>
