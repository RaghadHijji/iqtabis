{{-- "Are you sure?" for any form with data-confirm="question" (delete a quote, a comment…) --}}
<dialog id="confirm-dialog" class="auth-card glass m-auto w-[90%] max-w-sm rounded-3xl p-0 text-ink shadow-2xl backdrop:bg-black/40">
    <div class="flex flex-col gap-4 p-7 text-center">
        <span class="mx-auto grid h-14 w-14 place-items-center rounded-full bg-red-500/10 text-red-600"><x-icon name="trash" class="h-7 w-7" /></span>
        <h2 data-confirm-text class="text-xl font-extrabold"></h2>
        <p class="text-sm text-ink/70">{{ __('It will be removed for everyone and cannot be brought back.') }}</p>
        <div class="flex gap-2">
            <button type="button" data-confirm-yes class="h-11 flex-1 rounded-xl bg-red-600 font-bold text-white transition hover:bg-red-700">{{ __('Delete') }}</button>
            <button type="button" data-confirm-no class="h-11 flex-1 rounded-xl border border-ink/15 font-bold transition hover:bg-ink/5" autofocus>{{ __('Cancel') }}</button>
        </div>
    </div>
</dialog>
<script>
    (() => {
        const dialog = document.getElementById('confirm-dialog');
        let form = null;
        document.addEventListener('submit', (e) => {
            if (!e.target.matches('form[data-confirm]') || e.target.dataset.confirmed) return;
            e.preventDefault();
            form = e.target;
            dialog.querySelector('[data-confirm-text]').textContent = form.dataset.confirm;
            dialog.showModal();
        });
        dialog.querySelector('[data-confirm-yes]').addEventListener('click', () => {
            form.dataset.confirmed = '1';
            form.submit();
        });
        dialog.querySelector('[data-confirm-no]').addEventListener('click', () => dialog.close());
    })();
</script>
