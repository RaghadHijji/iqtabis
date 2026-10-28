{{-- Log in / sign up as a see-through pop-up in the middle of the page (FR-02, FR-03) --}}
@php
    // open it straight away when the form came back with errors, or after /login or /signup
    $openForm = old('form') ?? request('auth');
    if (! in_array($openForm, ['login', 'signup'])) {
        $openForm = null;
    }
@endphp

<div id="auth-modal" data-open="{{ $openForm }}"
    class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/35 p-4">

    <div class="auth-card glass relative w-full max-w-sm rounded-3xl p-7 shadow-2xl">
        <button type="button" data-auth-close aria-label="{{ __('Close') }}"
            class="absolute end-0 top-0 grid h-10 w-10 place-items-center rounded-es-2xl rounded-se-3xl bg-ink text-page transition hover:brightness-125">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
        </button>

        {{-- log in --}}
        <form data-auth-panel="login" action="{{ route('login') }}" method="POST" class="{{ $openForm === 'signup' ? 'hidden' : '' }}">
            @csrf
            <input type="hidden" name="form" value="login">
            <h2 class="mb-6 text-center text-2xl font-extrabold text-ink">{{ __('Log In') }}</h2>

            <label class="auth-field">
                <input name="email" type="email" value="{{ old('form') === 'login' ? old('email') : '' }}" placeholder=" " required
                    pattern="[^@\s]+@[^@\s]+\.[A-Za-z]{2,}" autocomplete="email">
                <span>{{ __('Email') }}</span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2.5" y="5" width="19" height="14" rx="2"/><path d="M3 6.5l9 6.5 9-6.5"/></svg>
            </label>
            @if (old('form') === 'login')
                @error('email') <p class="-mt-4 mb-4 text-xs font-bold text-red-600">{{ $message }}</p> @enderror
            @endif

            <label class="auth-field">
                <input name="password" type="password" placeholder=" " required minlength="8" autocomplete="current-password">
                <span>{{ __('Password') }}</span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M7 10V7a5 5 0 0 1 10 0v3h1a1 1 0 0 1 1 1v9a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1v-9a1 1 0 0 1 1-1h1zm2 0h6V7a3 3 0 0 0-6 0v3z"/></svg>
            </label>

            <div class="-mt-2 mb-4 flex items-center justify-between gap-3 text-xs font-bold text-ink/70">
                <label class="flex cursor-pointer items-center gap-2">
                    <input type="checkbox" name="remember" value="1" class="h-4 w-4 accent-accent" @checked(old('remember'))>
                    {{ __('Remember me') }}
                </label>
                <a href="{{ route('password.request') }}" class="hover:text-accent hover:underline">{{ __('Forgot your password?') }}</a>
            </div>

            <button type="submit" class="mt-1 h-11 w-full rounded-xl bg-ink font-bold text-page transition hover:-translate-y-0.5 hover:shadow-lg">{{ __('Log In') }}</button>

            <p class="mt-5 text-center text-sm text-ink/80">
                {{ __("Don't have an account?") }}
                <a href="{{ route('signup') }}" data-auth-switch="signup" class="font-bold text-accent hover:underline">{{ __('Sign Up') }}</a>
            </p>
        </form>

        {{-- sign up --}}
        <form data-auth-panel="signup" action="{{ route('signup') }}" method="POST" class="auth-compact {{ $openForm === 'signup' ? '' : 'hidden' }}">
            @csrf
            <input type="hidden" name="form" value="signup">
            <h2 class="mb-6 text-center text-2xl font-extrabold text-ink">{{ __('Sign Up') }}</h2>

            <label class="auth-field">
                <input name="name" type="text" value="{{ old('form') === 'signup' ? old('name') : '' }}" placeholder=" " required
                    minlength="2" maxlength="50" pattern="[A-Za-z؀-ۿ ]+" autocomplete="name">
                <span>{{ __('Name') }}</span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21v-1a6 6 0 0 1 6-6h4a6 6 0 0 1 6 6v1"/></svg>
            </label>
            @if (old('form') === 'signup')
                @error('name') <p class="-mt-4 mb-4 text-xs font-bold text-red-600">{{ $message }}</p> @enderror
            @endif

            <label class="auth-field">
                <input name="email" type="email" value="{{ old('form') === 'signup' ? old('email') : '' }}" placeholder=" " required
                    pattern="[^@\s]+@[^@\s]+\.[A-Za-z]{2,}" autocomplete="email">
                <span>{{ __('Email') }}</span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2.5" y="5" width="19" height="14" rx="2"/><path d="M3 6.5l9 6.5 9-6.5"/></svg>
            </label>
            @if (old('form') === 'signup')
                @error('email') <p class="-mt-4 mb-4 text-xs font-bold text-red-600">{{ $message }}</p> @enderror
            @endif

            <label class="auth-field">
                <input name="password" type="password" placeholder=" " required minlength="8" autocomplete="new-password">
                <span>{{ __('Password') }} <small class="opacity-70">({{ __('At least 8 characters.') }})</small></span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M7 10V7a5 5 0 0 1 10 0v3h1a1 1 0 0 1 1 1v9a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1v-9a1 1 0 0 1 1-1h1zm2 0h6V7a3 3 0 0 0-6 0v3z"/></svg>
            </label>
            @if (old('form') === 'signup')
                @error('password') <p class="-mt-4 mb-4 text-xs font-bold text-red-600">{{ $message }}</p> @enderror
            @endif

            <label class="auth-field">
                <input name="password_confirmation" type="password" placeholder=" " required minlength="8" autocomplete="new-password">
                <span>{{ __('Confirm password') }}</span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M7 10V7a5 5 0 0 1 10 0v3h1a1 1 0 0 1 1 1v9a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1v-9a1 1 0 0 1 1-1h1zm2 0h6V7a3 3 0 0 0-6 0v3z"/></svg>
            </label>

            <button type="submit" class="mt-1 h-11 w-full rounded-xl bg-ink font-bold text-page transition hover:-translate-y-0.5 hover:shadow-lg">{{ __('Sign Up') }}</button>

            <p class="mt-3 text-center text-xs text-ink/80">
                {!! __('By signing up you agree to the :terms and :privacy.', [
                    'terms' => '<a href="'.route('terms').'" class="font-bold text-accent hover:underline">'.e(__('Terms')).'</a>',
                    'privacy' => '<a href="'.route('privacy').'" class="font-bold text-accent hover:underline">'.e(__('Privacy')).'</a>',
                ]) !!}
            </p>

            <p class="mt-5 text-center text-sm text-ink/80">
                {{ __('Already have an account?') }}
                <a href="{{ route('login') }}" data-auth-switch="login" class="font-bold text-accent hover:underline">{{ __('Log In') }}</a>
            </p>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('auth-modal');
        const urls = { login: @json(route('login')), signup: @json(route('signup')) };

        const show = (which) => {
            modal.querySelectorAll('[data-auth-panel]').forEach(f => f.classList.toggle('hidden', f.dataset.authPanel !== which));
            modal.classList.replace('hidden', 'flex');
            document.body.classList.add('overflow-hidden');
            modal.querySelector(`[data-auth-panel="${which}"] input:not([type=hidden])`)?.focus();
        };
        const hide = () => {
            modal.classList.replace('flex', 'hidden');
            document.body.classList.remove('overflow-hidden');
        };

        // every "Log In" / "Sign Up" link on the page opens the pop-up instead of a new page
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a[href]');
            if (!link) return;
            const which = Object.keys(urls).find(k => link.href === urls[k]);
            if (which) { e.preventDefault(); show(which); }
        });

        modal.querySelector('[data-auth-close]').addEventListener('click', hide);
        modal.addEventListener('click', (e) => { if (e.target === modal) hide(); });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') hide(); });

        if (modal.dataset.open) show(modal.dataset.open);
    });
</script>
