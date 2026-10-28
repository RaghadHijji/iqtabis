{{-- My account: a see-through pop-up like log in, opened by pressing my name in the navbar --}}
@php
    $user = auth()->user();
    // which part to open again after saving, or when a form came back with errors
    $sent = str_starts_with((string) old('form'), 'account-') ? old('form') : session('account_section');
    $openAccount = $sent || session('account_status');
    $supportEmail = config('app.support_email');
    $row = 'flex cursor-pointer list-none items-center gap-3 rounded-xl px-3 py-3 font-bold text-ink transition hover:bg-card/50 [&::-webkit-details-marker]:hidden';
    $chevron = '<svg class="ms-auto h-4 w-4 opacity-60 transition group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>';
    $saveBtn = 'h-10 w-full rounded-xl bg-ink text-sm font-bold text-page transition hover:-translate-y-0.5 hover:shadow-lg';
@endphp

<div id="account-modal" data-open="{{ $openAccount ? '1' : '' }}"
    class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/35 p-4">

    <div class="auth-card glass relative flex max-h-[88vh] w-full max-w-sm flex-col overflow-hidden rounded-3xl shadow-2xl">
        <button type="button" data-account-close aria-label="{{ __('Close') }}"
            class="absolute end-0 top-0 z-10 grid h-10 w-10 place-items-center rounded-es-2xl rounded-se-3xl bg-ink text-page transition hover:brightness-125">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
        </button>

        <div class="no-scrollbar overflow-y-auto p-6">
            {{-- who I am --}}
            <div class="flex flex-col items-center gap-2 text-center">
                <x-avatar :user="$user" size="h-20 w-20 text-3xl" class="ring-4 ring-white/70 shadow-lg" />
                <p class="text-xl font-extrabold text-ink">{{ $user->name }}</p>
                <p class="text-sm text-ink/70">{{ $user->email }}</p>
                <div class="flex flex-wrap justify-center gap-2 text-sm font-bold">
                    <a href="{{ route('home', ['view' => 'mine']) }}#quotes" class="rounded-full bg-accent/10 px-3 py-1 text-accent transition hover:bg-accent hover:text-on-accent">{{ __('My quotes') }}</a>
                    <a href="{{ route('home', ['view' => 'likes']) }}#quotes" class="rounded-full bg-accent/10 px-3 py-1 text-accent transition hover:bg-accent hover:text-on-accent">{{ __('My likes') }}</a>
                    <a href="{{ route('home', ['view' => 'favs']) }}#quotes" class="rounded-full bg-accent/10 px-3 py-1 text-accent transition hover:bg-accent hover:text-on-accent">{{ __('Saved') }}</a>
                    <a href="{{ route('home', ['by' => auth()->id()]) }}#quotes" class="rounded-full bg-accent/10 px-3 py-1 text-accent transition hover:bg-accent hover:text-on-accent">{{ __('My page') }}</a>
                    @can('admin')
                        <a href="{{ route('admin.index') }}" class="rounded-full bg-ink px-3 py-1 text-page transition hover:bg-accent hover:text-on-accent">{{ __('Admin dashboard') }}</a>
                    @endcan
                </div>
            </div>

            @if (session('account_status'))
                <p class="mt-4 rounded-xl bg-emerald-500/15 px-4 py-2 text-center text-sm font-bold text-emerald-700">✓ {{ session('account_status') }}</p>
            @endif

            {{-- Account settings --}}
            <h3 class="mb-1 mt-6 text-xs font-extrabold tracking-[0.2em] text-ink/70 rtl:tracking-normal">{{ __('ACCOUNT SETTINGS') }}</h3>
            <div class="flex flex-col">

                {{-- change avatar --}}
                <details name="account" class="group" @if ($sent === 'account-avatar') open @endif>
                    <summary class="{{ $row }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M9 3 7.2 5H4a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-3.2L15 3H9zm3 5a5 5 0 1 1 0 10 5 5 0 0 1 0-10zm0 2a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/></svg>
                        {{ __('Change avatar') }} {!! $chevron !!}
                    </summary>
                    <div class="flex flex-col gap-3 px-3 pb-4 pt-1">
                        <form action="{{ route('avatar.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="form" value="account-avatar">
                            <label class="{{ $saveBtn }} flex cursor-pointer items-center justify-center gap-2">
                                {{ __('Upload a picture') }}
                                <input type="file" name="avatar" accept="image/png,image/jpeg,image/webp" class="hidden" onchange="this.form.submit()">
                            </label>
                        </form>
                        <p class="text-xs text-ink/70">{{ __('JPG, PNG or WEBP, up to 2 MB.') }}</p>
                        @error('avatar') <p class="text-xs font-bold text-red-600">{{ $message }}</p> @enderror
                        @if ($user->avatar)
                            <form action="{{ route('avatar.remove') }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="form" value="account-avatar">
                                <button type="submit" class="text-sm font-bold text-ink/70 hover:text-red-600">{{ __('Remove picture') }}</button>
                            </form>
                        @endif
                    </div>
                </details>

                {{-- change email --}}
                <details name="account" class="group" @if ($sent === 'account-email') open @endif>
                    <summary class="{{ $row }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2.5" y="5" width="19" height="14" rx="2"/><path d="M3 6.5l9 6.5 9-6.5"/></svg>
                        {{ __('Change email') }} {!! $chevron !!}
                    </summary>
                    <form action="{{ route('account.email') }}" method="POST" class="auth-compact px-3 pb-4 pt-2">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="form" value="account-email">
                        <label class="auth-field">
                            <input name="email" type="email" value="{{ $sent === 'account-email' ? old('email') : $user->email }}" placeholder=" " required autocomplete="email">
                            <span>{{ __('New email') }}</span>
                        </label>
                        @if ($sent === 'account-email') @error('email') <p class="-mt-2 mb-3 text-xs font-bold text-red-600">{{ $message }}</p> @enderror @endif
                        <label class="auth-field">
                            <input name="current_password" type="password" placeholder=" " required autocomplete="current-password">
                            <span>{{ __('Current password') }}</span>
                        </label>
                        @if ($sent === 'account-email') @error('current_password') <p class="-mt-2 mb-3 text-xs font-bold text-red-600">{{ $message }}</p> @enderror @endif
                        <button type="submit" class="{{ $saveBtn }}">{{ __('Save') }}</button>
                    </form>
                </details>

                {{-- change password --}}
                <details name="account" class="group" @if ($sent === 'account-password') open @endif>
                    <summary class="{{ $row }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M7 10V7a5 5 0 0 1 10 0v3h1a1 1 0 0 1 1 1v9a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1v-9a1 1 0 0 1 1-1h1zm2 0h6V7a3 3 0 0 0-6 0v3z"/></svg>
                        {{ __('Change password') }} {!! $chevron !!}
                    </summary>
                    <form action="{{ route('account.password') }}" method="POST" class="auth-compact px-3 pb-4 pt-2">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="form" value="account-password">
                        <label class="auth-field">
                            <input name="current_password" type="password" placeholder=" " required autocomplete="current-password">
                            <span>{{ __('Current password') }}</span>
                        </label>
                        @if ($sent === 'account-password') @error('current_password') <p class="-mt-2 mb-3 text-xs font-bold text-red-600">{{ $message }}</p> @enderror @endif
                        <label class="auth-field">
                            <input name="password" type="password" placeholder=" " required minlength="8" autocomplete="new-password">
                            <span>{{ __('New password') }}</span>
                        </label>
                        @if ($sent === 'account-password') @error('password') <p class="-mt-2 mb-3 text-xs font-bold text-red-600">{{ $message }}</p> @enderror @endif
                        <label class="auth-field">
                            <input name="password_confirmation" type="password" placeholder=" " required minlength="8" autocomplete="new-password">
                            <span>{{ __('Confirm new password') }}</span>
                        </label>
                        <button type="submit" class="{{ $saveBtn }}">{{ __('Save') }}</button>
                    </form>
                </details>
            </div>

            <div class="my-3 border-t border-ink/10"></div>

            {{-- Support --}}
            <details name="account" class="group">
                <summary class="{{ $row }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M9.1 9a3 3 0 0 1 5.8 1c0 2-3 3-3 3M12 17h.01"/></svg>
                    {{ __('Support') }} {!! $chevron !!}
                </summary>
                <div class="px-3 pb-4 pt-1 text-sm leading-relaxed text-ink/75">
                    <p>{{ __('Found a problem or have an idea? We would love to hear from you.') }}</p>
                    @if ($supportEmail)
                        <a href="mailto:{{ $supportEmail }}" class="mt-2 inline-block font-bold text-accent hover:underline" dir="ltr">{{ $supportEmail }}</a>
                    @endif
                </div>
            </details>

            {{-- About --}}
            <details name="account" class="group">
                <summary class="{{ $row }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
                    {{ __('About Iqtabis') }} {!! $chevron !!}
                </summary>
                <div class="px-3 pb-4 pt-1 text-sm leading-relaxed text-ink/75">
                    <p>{{ __('Iqtabis is a home for quotes: find words for every moment, browse by category, like and save the ones that touch you, and share your own.') }}</p>
                    <p class="mt-2 text-xs text-ink/70">{{ __('Made by Raghad Hijji · Tuwaiq Bootcamp 2026') }}</p>
                </div>
            </details>

            <div class="my-3 border-t border-ink/10"></div>

            {{-- Delete account (red) --}}
            <details name="account" class="group" @if ($sent === 'account-delete') open @endif>
                <summary class="{{ $row }} !text-red-600 hover:!bg-red-500/10">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6"/></svg>
                    {{ __('Delete account') }} {!! $chevron !!}
                </summary>
                <form id="delete-account-form" action="{{ route('account.destroy') }}" method="POST" class="auth-compact px-3 pb-4 pt-2">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="form" value="account-delete">
                    <p class="mb-4 rounded-xl bg-red-500/10 p-3 text-xs leading-relaxed text-red-700">{{ __('This deletes your account and all your quotes, likes and favorites forever.') }}</p>
                    <label class="auth-field">
                        <input name="delete_password" type="password" placeholder=" " required autocomplete="current-password">
                        <span>{{ __('Your password') }}</span>
                    </label>
                    @error('delete_password') <p class="-mt-2 mb-3 text-xs font-bold text-red-600">{{ $message }}</p> @enderror
                    {{-- asks one last time before deleting (see #delete-account-dialog) --}}
                    <button type="button" data-delete-account class="h-10 w-full rounded-xl bg-red-600 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:bg-red-700 hover:shadow-lg">{{ __('Delete my account') }}</button>
                </form>
            </details>
        </div>
    </div>
</div>

{{-- "are you sure?" before the account is deleted for good --}}
<dialog id="delete-account-dialog" class="auth-card glass m-auto w-[90%] max-w-sm rounded-3xl p-0 text-ink shadow-2xl backdrop:bg-black/50">
    <div class="flex flex-col gap-4 p-7 text-center">
        <span class="mx-auto grid h-14 w-14 place-items-center rounded-full bg-red-500/10 text-red-600">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 9v4M12 17h.01M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/></svg>
        </span>
        <h2 class="text-xl font-extrabold">{{ __('Delete your account for good?') }}</h2>
        <p class="text-sm leading-relaxed text-ink/70">{{ __('All your quotes, likes and saved quotes will be deleted, and this cannot be undone.') }}</p>
        <div class="flex gap-2">
            <button type="button" data-delete-account-yes class="h-11 flex-1 rounded-xl bg-red-600 font-bold text-white transition hover:bg-red-700">{{ __('Yes, delete it') }}</button>
            <button type="button" data-delete-account-no class="h-11 flex-1 rounded-xl border border-ink/15 font-bold transition hover:bg-ink/5" autofocus>{{ __('Cancel') }}</button>
        </div>
    </div>
</dialog>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('account-modal');
        const show = () => {
            modal.classList.replace('hidden', 'flex');
            document.body.classList.add('overflow-hidden');
        };
        const hide = () => {
            modal.classList.replace('flex', 'hidden');
            document.body.classList.remove('overflow-hidden');
        };
        document.querySelectorAll('[data-account-open]').forEach((b) => b.addEventListener('click', show));
        modal.querySelector('[data-account-close]').addEventListener('click', hide);
        modal.addEventListener('click', (e) => { if (e.target === modal) hide(); });
        const confirmBox = document.getElementById('delete-account-dialog');
        const deleteForm = document.getElementById('delete-account-form');
        // check the password field first, then ask "are you sure?"
        modal.querySelector('[data-delete-account]').addEventListener('click', () => {
            if (deleteForm.reportValidity()) confirmBox.showModal();
        });
        confirmBox.querySelector('[data-delete-account-yes]').addEventListener('click', () => deleteForm.submit());
        confirmBox.querySelector('[data-delete-account-no]').addEventListener('click', () => confirmBox.close());
        // Escape closes only the question when it is open, not the whole account window
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && !confirmBox.open) hide(); });
        if (modal.dataset.open) show();
    });
</script>
