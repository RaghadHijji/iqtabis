@extends('Layout.app')

@section('title', Str::limit($quote->content, 40) . ' | اقتبس')
@section('og_title', '«' . Str::limit($quote->content, 90) . '»')
@section('og_description', ($quote->author ? '— ' . $quote->author . ' · ' : '') . __('A quote for every moment'))
@section('content')
    {{-- One quote on its own page, with its comments (FR-22) --}}
    <div class="page-photo pointer-events-none fixed inset-0 -z-10" aria-hidden="true">
        <div class="absolute inset-0 scale-110 bg-cover bg-center photo-warm" style="background-image: url('{{ asset('images/hero-video-poster.jpg') }}')"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-page/60 via-page/70 to-page/85"></div>
    </div>

    <div class="mx-auto flex max-w-3xl flex-col gap-6 px-6 py-12">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <x-back-link />
            <a href="{{ route('quotes.random') }}" class="btn-secondary inline-flex items-center gap-2">
                <x-icon name="shuffle" class="h-4 w-4" />{{ __('Quote me') }}
            </a>
        </div>

        <article class="flex flex-col gap-6 rounded-[2rem] border border-ink/10 bg-card p-8 text-ink shadow-sm md:p-10">
            <div class="flex items-center justify-between">
                <span class="h-12 w-12 text-accent/40">
                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M4 18v-5c0-3.3 1.8-6 5-7l.8 1.6C7.9 8.4 7 9.8 7 11h3v7H4zm10 0v-5c0-3.3 1.8-6 5-7l.8 1.6c-1.9.8-2.8 2.2-2.8 3.4h3v7h-6z"/></svg>
                </span>
                <a href="{{ route('home', ['filter' => $quote->category->name]) }}" class="rounded-full bg-accent/10 px-4 py-1.5 text-sm font-bold text-accent hover:bg-accent/20">{{ __($quote->category->name) }}</a>
            </div>

            <blockquote class="font-quote text-3xl leading-relaxed md:text-4xl md:leading-relaxed" dir="auto">{{ $quote->content }}</blockquote>
            @if ($quote->author)
                <p class="-mt-3 text-lg font-bold text-sand" dir="auto">— {{ $quote->author }}</p>
            @endif

            <div class="flex flex-wrap items-center gap-2 border-t border-ink/5 pt-5">
                <a href="{{ route('home', ['by' => $quote->user_id]) }}" class="group me-auto flex items-center gap-2">
                    <x-avatar :user="$quote->user" size="h-11 w-11 text-base" />
                    <span class="flex flex-col leading-tight">
                        <span class="font-bold group-hover:text-accent group-hover:underline">{{ $quote->user->name }}</span>
                        <span class="text-xs text-ink/70">{{ $quote->created_at->format('Y-m-d') }} · {{ $quote->created_at->hijri() }}</span>
                    </span>
                </a>

                {{-- the buttons: their own full line on phones, beside the name on bigger screens --}}
                <div class="flex w-full items-center justify-between gap-1 sm:w-auto sm:justify-start sm:gap-2">
                @auth
                    <form method="POST" action="{{ route('quotes.like', $quote) }}">
                        @csrf
                        <button type="submit" title="{{ $liked ? __('Unlike') : __('Like') }}" aria-pressed="{{ $liked ? 'true' : 'false' }}"
                            class="like-btn icon-btn {{ $liked ? 'text-red-600' : '' }}"><x-icon name="heart" class="{{ $liked ? 'fill-current' : '' }}" />{{ $quote->likes_count }}</button>
                    </form>
                    <form method="POST" action="{{ route('quotes.favorite', $quote) }}">
                        @csrf
                        <button type="submit" title="{{ $saved ? __('Unsave') : __('Save') }}" aria-label="{{ $saved ? __('Unsave') : __('Save') }}" aria-pressed="{{ $saved ? 'true' : 'false' }}"
                            class="icon-btn px-0 {{ $saved ? 'text-accent' : '' }}"><x-icon name="bookmark" class="{{ $saved ? 'fill-current' : '' }}" /></button>
                    </form>
                @else
                    <a href="{{ route('login') }}" title="{{ __('Like') }}" class="like-btn icon-btn"><x-icon name="heart" />{{ $quote->likes_count }}</a>
                @endauth

                <a href="#comments" title="{{ __('Comments') }}" aria-label="{{ __('Comments') }}" class="icon-btn"><x-icon name="comment" />{{ $quote->comments->count() }}</a>

                <button type="button" data-copy="{{ $quote->content }}" data-author="{{ $quote->author }}" title="{{ __('Copy quote') }}" aria-label="{{ __('Copy quote') }}" class="icon-btn px-0"><x-icon name="copy" /></button>
                <button type="button" data-image data-text="{{ $quote->content }}" data-author="{{ $quote->author }}" data-category="{{ __($quote->category->name) }}"
                    title="{{ __('Save as image') }}" aria-label="{{ __('Save as image') }}" class="icon-btn px-0"><x-icon name="image" /></button>

                @can('delete', $quote)
                    <form method="POST" action="{{ route('quotes.destroy', $quote) }}" data-confirm="{{ __('Delete this quote?') }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" title="{{ __('Delete') }}" aria-label="{{ __('Delete') }}" class="icon-btn px-0 hover:bg-red-500/10 hover:text-red-600"><x-icon name="trash" /></button>
                    </form>
                @endcan
                </div>
            </div>
        </article>

        {{-- Comments --}}
        <section id="comments" class="flex scroll-mt-28 flex-col gap-4">
            <h2 class="text-2xl font-extrabold text-ink">{{ __('Comments') }} <span class="text-ink/70">({{ $quote->comments->count() }})</span></h2>

            @auth
                @if (auth()->user()->hasVerifiedEmail())
                    <form method="POST" action="{{ route('comments.store', $quote) }}" class="flex flex-col gap-2 rounded-3xl border border-ink/10 bg-card p-4 shadow-sm">
                        @csrf
                        <div class="flex items-start gap-3">
                            <x-avatar :user="auth()->user()" />
                            <textarea name="body" rows="2" required minlength="2" maxlength="300" dir="auto" placeholder="{{ __('Write a comment…') }}"
                                class="input min-h-12 flex-1 resize-none">{{ old('body') }}</textarea>
                        </div>
                        @error('body') <p class="text-sm font-bold text-red-600">{{ $message }}</p> @enderror
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-ink/70">{{ __('2–300 characters.') }}</span>
                            <button type="submit" class="btn-primary">{{ __('Comment') }}</button>
                        </div>
                    </form>
                @else
                    <p class="rounded-3xl border border-dashed border-ink/20 bg-card/60 p-5 text-center text-ink/70">
                        {{ __('Confirm your email to share quotes and comment.') }}
                        <a href="{{ route('verification.notice') }}" class="font-bold text-accent hover:underline">{{ __('Confirm now') }}</a>
                    </p>
                @endif
            @else
                <p class="rounded-3xl border border-dashed border-ink/20 bg-card/60 p-5 text-center text-ink/70">
                    <a href="{{ route('login') }}" class="font-bold text-accent hover:underline">{{ __('Log in') }}</a> {{ __('to write a comment.') }}
                </p>
            @endauth

            @forelse ($quote->comments as $comment)
                <div id="comment-{{ $comment->id }}" class="flex scroll-mt-28 gap-3 rounded-3xl border border-ink/10 bg-card p-5 shadow-sm">
                    <a href="{{ route('home', ['by' => $comment->user_id]) }}"><x-avatar :user="$comment->user" /></a>
                    <div class="flex min-w-0 flex-1 flex-col gap-1">
                        <div class="flex items-center gap-2 text-sm">
                            <a href="{{ route('home', ['by' => $comment->user_id]) }}" class="font-bold hover:text-accent hover:underline">{{ $comment->user->name }}</a>
                            @if ($comment->user_id === $quote->user_id)
                                <span class="rounded-full bg-sand/15 px-2 py-0.5 text-[11px] font-bold text-sand">{{ __('Poster') }}</span>
                            @endif
                            <span class="text-xs text-ink/70">{{ $comment->created_at->diffForHumans() }}@if ($comment->updated_at->gt($comment->created_at)) · {{ __('edited') }}@endif</span>
                            @if (auth()->check() && auth()->id() !== $comment->user_id)
                                <button type="button" data-report="{{ route('comments.report', $comment) }}" title="{{ __('Report') }}" aria-label="{{ __('Report') }}"
                                    class="tap ms-auto grid h-9 w-9 place-items-center rounded-full text-ink/70 transition hover:bg-red-500/10 hover:text-red-600"><x-icon name="flag" class="h-4 w-4" /></button>
                            @endif
                            {{-- the writer can edit for 10 minutes after writing --}}
                            @can('update', $comment)
                                <button type="button" data-edit-comment="{{ $comment->id }}" title="{{ __('Edit') }}" aria-label="{{ __('Edit') }}"
                                    class="tap ms-auto grid h-9 w-9 place-items-center rounded-full text-ink/70 transition hover:bg-ink/5 hover:text-accent"><x-icon name="pen" class="h-4 w-4" /></button>
                            @endcan
                            @can('delete', $comment)
                                <form method="POST" action="{{ route('comments.destroy', $comment) }}" data-confirm="{{ __('Delete this comment?') }}" class="{{ auth()->id() !== $comment->user_id || auth()->user()->can('update', $comment) ? '' : 'ms-auto' }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="{{ __('Delete') }}" aria-label="{{ __('Delete') }}" class="tap grid h-9 w-9 place-items-center rounded-full text-ink/70 transition hover:bg-red-500/10 hover:text-red-600"><x-icon name="trash" class="h-4 w-4" /></button>
                                </form>
                            @endcan
                        </div>
                        <p data-comment-body="{{ $comment->id }}" class="whitespace-pre-line break-words leading-relaxed" dir="auto">{{ $comment->body }}</p>
                        @can('update', $comment)
                            <form method="POST" action="{{ route('comments.update', $comment) }}" data-comment-form="{{ $comment->id }}" class="hidden flex-col gap-2">
                                @csrf
                                @method('PUT')
                                <textarea name="body" rows="2" required minlength="2" maxlength="300" dir="auto" class="input resize-none">{{ $comment->body }}</textarea>
                                <div class="flex items-center gap-2">
                                    <button type="submit" class="btn-primary px-4 py-1.5 text-sm">{{ __('Save') }}</button>
                                    <button type="button" data-edit-comment="{{ $comment->id }}" class="rounded-2xl px-4 py-1.5 text-sm font-bold text-ink/70 hover:bg-ink/5">{{ __('Cancel') }}</button>
                                </div>
                            </form>
                        @endcan
                    </div>
                </div>
            @empty
                <p class="py-6 text-center text-ink/70">{{ __('No comments yet. Be the first!') }}</p>
            @endforelse
        </section>

        {{-- more quotes from the same category --}}
        @if ($similar->isNotEmpty())
            <section class="flex flex-col gap-4">
                <h2 class="text-2xl font-extrabold text-ink">{{ __('Similar quotes') }}</h2>
                <div class="grid gap-4 sm:grid-cols-3">
                    @foreach ($similar as $other)
                        <a href="{{ route('quotes.show', $other) }}"
                            class="flex flex-col gap-3 rounded-3xl border border-ink/10 bg-card p-5 text-ink shadow-sm transition hover:-translate-y-0.5 hover:border-accent hover:shadow-md">
                            <p class="font-quote line-clamp-4 flex-1 text-lg leading-relaxed" dir="auto">{{ $other->content }}</p>
                            <div class="flex items-center justify-between gap-2 text-sm text-ink/70">
                                <span class="truncate" dir="auto">— {{ $other->author ?: $other->user->name }}</span>
                                <span class="flex shrink-0 items-center gap-1 font-bold text-rose-500"><x-icon name="heart" class="h-4 w-4" />{{ $other->likes_count }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </div>

    @auth
        <x-report-dialog :title="__('Report this comment')" :question="__('Why is this comment not suitable?')" />
    @endauth

    <x-confirm-dialog />

    {{-- the pen opens the edit box in place of the comment, Cancel closes it --}}
    <script>
        document.addEventListener('click', (event) => {
            const toggle = event.target.closest('[data-edit-comment]');
            if (!toggle) return;
            const id = toggle.dataset.editComment;
            const form = document.querySelector(`[data-comment-form="${id}"]`);
            const open = form.classList.contains('hidden');
            form.classList.toggle('hidden', !open);
            form.classList.toggle('flex', open);
            document.querySelector(`[data-comment-body="${id}"]`).classList.toggle('hidden', open);
            if (open) form.querySelector('textarea').focus();
        });
    </script>
@endsection
