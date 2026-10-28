{{-- Report pop-up: pick why a quote or a comment is not suitable (FR-15).
     Any button with data-report="url" opens it and sends the report to that url. --}}
@props(['title', 'question', 'soft' => false])

<dialog id="report-dialog" class="auth-card glass m-auto w-[90%] max-w-sm rounded-3xl p-0 text-ink shadow-2xl backdrop:bg-black/40">
    <form @if ($soft) data-soft @endif method="POST" action="" class="flex flex-col gap-4 p-7">
        @csrf
        <h2 class="text-xl font-extrabold">{{ $title }}</h2>
        <p class="text-sm text-ink/70">{{ $question }}</p>
        @foreach (config('moderation.report_reasons') as $reason => $label)
            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-ink/10 px-4 py-3 transition hover:border-accent has-checked:border-accent has-checked:bg-accent/10">
                <input type="radio" name="reason" value="{{ $reason }}" required class="accent-[var(--color-accent)]" @checked($loop->first)>
                <span class="font-bold">{{ __($label) }}</span>
            </label>
        @endforeach
        <div class="flex gap-2">
            <button type="submit" class="h-11 flex-1 rounded-xl bg-red-600 font-bold text-white transition hover:bg-red-700">{{ __('Report') }}</button>
            <button type="button" data-report-cancel class="h-11 flex-1 rounded-xl border border-ink/15 font-bold transition hover:bg-ink/5">{{ __('Cancel') }}</button>
        </div>
    </form>
</dialog>

<script>
    (() => {
        const dialog = document.getElementById('report-dialog');
        document.addEventListener('click', (e) => {
            const button = e.target.closest('[data-report]');
            if (button) {
                dialog.querySelector('form').action = button.dataset.report;
                dialog.showModal();
            }
            if (e.target.closest('[data-report-cancel]')) dialog.close();
        });
    })();
</script>
