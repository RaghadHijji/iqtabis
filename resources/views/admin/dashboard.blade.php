@extends('Layout.app')

@section('title', __('Admin dashboard') . ' | اقتبس')
@section('content')
    {{-- Admin dashboard: numbers, reported quotes, top quotes and new users (FR-25) --}}
    <div class="mx-auto flex max-w-6xl flex-col gap-8 px-6 py-12">
        <x-back-link class="self-start" />
        <div class="flex items-center gap-3">
            <span class="grid h-12 w-12 place-items-center rounded-2xl bg-accent text-on-accent"><x-icon name="shield" class="h-6 w-6" /></span>
            <div>
                <h1 class="text-3xl font-extrabold text-ink">{{ __('Admin dashboard') }}</h1>
                <p class="text-sm text-ink/70">{{ __('Keep Iqtabis clean and see how it is doing.') }}</p>
            </div>
        </div>

        {{-- numbers --}}
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
            @foreach ([
                ['users', __('Users'), 'users', 'text-accent'],
                ['quotes', __('Quotes'), 'copy', 'text-ink'],
                ['comments', __('Comments'), 'comment', 'text-sand'],
                ['likes', __('Likes'), 'heart', 'text-rose-500'],
                ['reports', __('Reports'), 'flag', 'text-red-600'],
            ] as [$key, $label, $icon, $color])
                <div class="flex flex-col gap-2 rounded-3xl border border-ink/10 bg-card p-5 shadow-sm">
                    <x-icon :name="$icon" class="h-5 w-5 {{ $color }}" />
                    <p class="text-3xl font-extrabold text-ink">{{ $stats[$key] }}</p>
                    <p class="text-sm text-ink/70">{{ $label }}</p>
                </div>
            @endforeach
        </div>

        {{-- reported quotes --}}
        <section class="flex flex-col gap-4">
            <h2 class="text-2xl font-extrabold text-ink">{{ __('Reported quotes') }}</h2>
            <p class="-mt-2 text-sm text-ink/70">{{ __('A quote with :count reports is hidden from everyone until you decide.', ['count' => $hideAfter]) }}</p>

            @forelse ($reported as $quote)
                @php $hidden = $quote->reports_count >= $hideAfter; @endphp
                <div class="flex flex-col gap-4 rounded-3xl border {{ $hidden ? 'border-red-300' : 'border-ink/10' }} bg-card p-5 shadow-sm md:flex-row md:items-center">
                    <div class="flex min-w-0 flex-1 flex-col gap-2">
                        <div class="flex flex-wrap items-center gap-2 text-xs">
                            <span class="rounded-full bg-red-500/10 px-3 py-1 font-bold text-red-600">{{ trans_choice(':count report|:count reports', $quote->reports_count) }}</span>
                            @if ($hidden)
                                <span class="rounded-full bg-ink/10 px-3 py-1 font-bold text-ink/70">{{ __('Hidden') }}</span>
                            @endif
                            <span class="rounded-full bg-accent/10 px-3 py-1 font-bold text-accent">{{ __($quote->category->name) }}</span>
                            <span class="text-ink/70">{{ __('by :name', ['name' => $quote->user->name]) }}</span>
                        </div>
                        <p class="font-quote text-lg leading-relaxed" dir="auto">{{ $quote->content }}</p>
                        <p class="text-xs text-ink/70">
                            {{ __('Reasons') }}:
                            {{ $quote->reports->countBy('pivot.reason')->map(fn ($n, $reason) => __(config('moderation.report_reasons')[$reason] ?? $reason) . " ($n)")->join('، ') }}
                        </p>
                    </div>
                    <div class="flex shrink-0 gap-2">
                        <form method="POST" action="{{ route('admin.restore', $quote) }}">
                            @csrf
                            <button type="submit" class="inline-flex h-11 items-center gap-2 rounded-xl border border-ink/15 px-4 font-bold text-ink transition hover:border-accent hover:text-accent"><x-icon name="check" class="h-4 w-4" />{{ __('Keep it') }}</button>
                        </form>
                        <form method="POST" action="{{ route('quotes.destroy', $quote) }}" data-confirm="{{ __('Delete this quote?') }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex h-11 items-center gap-2 rounded-xl bg-red-600 px-4 font-bold text-white transition hover:bg-red-700"><x-icon name="trash" class="h-4 w-4" />{{ __('Delete') }}</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="rounded-3xl border border-dashed border-ink/20 p-8 text-center text-ink/70">{{ __('No reported quotes.') }}</p>
            @endforelse
        </section>

        {{-- reported comments: same rule as quotes --}}
        <section class="flex flex-col gap-4">
            <h2 class="text-2xl font-extrabold text-ink">{{ __('Reported comments') }}</h2>

            @forelse ($reportedComments as $comment)
                @php $hidden = $comment->reports_count >= $hideAfter; @endphp
                <div class="flex flex-col gap-4 rounded-3xl border {{ $hidden ? 'border-red-300' : 'border-ink/10' }} bg-card p-5 shadow-sm md:flex-row md:items-center">
                    <div class="flex min-w-0 flex-1 flex-col gap-2">
                        <div class="flex flex-wrap items-center gap-2 text-xs">
                            <span class="rounded-full bg-red-500/10 px-3 py-1 font-bold text-red-600">{{ trans_choice(':count report|:count reports', $comment->reports_count) }}</span>
                            @if ($hidden)
                                <span class="rounded-full bg-ink/10 px-3 py-1 font-bold text-ink/70">{{ __('Hidden') }}</span>
                            @endif
                            <span class="text-ink/70">{{ __('by :name', ['name' => $comment->user->name]) }}</span>
                            <a href="{{ route('quotes.show', $comment->quote) }}#comments" class="font-bold text-accent hover:underline">{{ __('On the quote') }}</a>
                        </div>
                        <p class="whitespace-pre-line break-words leading-relaxed" dir="auto">{{ $comment->body }}</p>
                        <p class="text-xs text-ink/70">
                            {{ __('Reasons') }}:
                            {{ $comment->reports->countBy('pivot.reason')->map(fn ($n, $reason) => __(config('moderation.report_reasons')[$reason] ?? $reason) . " ($n)")->join('، ') }}
                        </p>
                    </div>
                    <div class="flex shrink-0 gap-2">
                        <form method="POST" action="{{ route('admin.comments.restore', $comment) }}">
                            @csrf
                            <button type="submit" class="inline-flex h-11 items-center gap-2 rounded-xl border border-ink/15 px-4 font-bold text-ink transition hover:border-accent hover:text-accent"><x-icon name="check" class="h-4 w-4" />{{ __('Keep it') }}</button>
                        </form>
                        <form method="POST" action="{{ route('comments.destroy', $comment) }}" data-confirm="{{ __('Delete this comment?') }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex h-11 items-center gap-2 rounded-xl bg-red-600 px-4 font-bold text-white transition hover:bg-red-700"><x-icon name="trash" class="h-4 w-4" />{{ __('Delete') }}</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="rounded-3xl border border-dashed border-ink/20 p-8 text-center text-ink/70">{{ __('No reported comments.') }}</p>
            @endforelse
        </section>

        <div class="grid gap-6 lg:grid-cols-2">
            {{-- most liked --}}
            <section class="flex flex-col gap-3 rounded-3xl border border-ink/10 bg-card p-6 shadow-sm">
                <h2 class="text-xl font-extrabold text-ink">{{ __('Most liked quotes') }}</h2>
                @forelse ($topQuotes as $quote)
                    <a href="{{ route('quotes.show', $quote) }}" class="flex items-center gap-3 rounded-2xl p-2 transition hover:bg-accent/5">
                        <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-accent text-sm font-extrabold text-on-accent">{{ $loop->iteration }}</span>
                        <span class="min-w-0 flex-1 truncate font-quote" dir="auto">{{ $quote->content }}</span>
                        <span class="flex shrink-0 items-center gap-1 text-sm font-bold text-rose-500"><x-icon name="heart" class="h-4 w-4 fill-current" />{{ $quote->likes_count }}</span>
                    </a>
                @empty
                    <p class="text-sm text-ink/70">{{ __('No quotes found.') }}</p>
                @endforelse
            </section>

            {{-- newest users --}}
            <section class="flex flex-col gap-3 rounded-3xl border border-ink/10 bg-card p-6 shadow-sm">
                <h2 class="text-xl font-extrabold text-ink">{{ __('Newest users') }}</h2>
                @foreach ($newUsers as $member)
                    <a href="{{ route('home', ['by' => $member->id]) }}" class="flex items-center gap-3 rounded-2xl p-2 transition hover:bg-accent/5">
                        <x-avatar :user="$member" />
                        <span class="flex min-w-0 flex-1 flex-col leading-tight">
                            <span class="font-bold">{{ $member->name }}
                                @if ($member->is_admin)
                                    <span class="ms-1 rounded-full bg-accent/10 px-2 py-0.5 text-[11px] text-accent">{{ __('Admin') }}</span>
                                @endif
                            </span>
                            <span class="truncate text-xs text-ink/70">{{ $member->email }} · {{ $member->created_at->diffForHumans() }}</span>
                        </span>
                        <span class="shrink-0 text-sm text-ink/70">{{ __(':count quotes', ['count' => $member->quotes_count]) }}</span>
                    </a>
                @endforeach
            </section>
        </div>
    </div>

    <x-confirm-dialog />
@endsection
