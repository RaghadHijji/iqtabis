{{-- Small helpers for every form on the site:
     a Caps Lock warning and a strength bar on password fields,
     "Sending…" on the button so a form is not sent twice,
     and a warning before leaving a quote you have not sent --}}
@php
    $text = [
        'caps' => __('Caps Lock is on'),
        'levels' => [__('Weak'), __('Medium'), __('Strong')],
        'strength' => __('Password strength'),
        'sending' => __('Sending…'),
    ];
@endphp
<script>
    (() => {
        const text = @json($text);

        // Caps Lock warning, under every password field
        document.querySelectorAll('.auth-field input[type=password]').forEach((input) => {
            const hint = document.createElement('p');
            hint.className = 'pw-hint hidden';
            hint.textContent = text.caps;
            hint.setAttribute('role', 'status');
            input.closest('.auth-field').after(hint);

            const check = (e) => hint.classList.toggle('hidden', !e.getModifierState?.('CapsLock'));
            input.addEventListener('keydown', check);
            input.addEventListener('keyup', check);
            input.addEventListener('blur', () => hint.classList.add('hidden'));
        });

        // strength bar, only where you choose a new password
        // one point each for: 8+ letters, 12+ letters, letters and numbers, capitals or symbols
        const score = (value) => [
            value.length >= 8,
            value.length >= 12,
            /\p{L}/u.test(value) && /\d/.test(value),
            /[A-Z]/.test(value) && /[a-z]/.test(value) || /[^\p{L}\d\s]/u.test(value),
        ].filter(Boolean).length;

        document.querySelectorAll('.auth-field input[name=password][autocomplete=new-password]').forEach((input) => {
            const meter = document.createElement('div');
            meter.className = 'pw-meter hidden';
            meter.innerHTML = '<span class="pw-bar"><i></i></span><small></small>';
            meter.setAttribute('aria-live', 'polite');
            input.closest('.auth-field').after(meter);

            input.addEventListener('input', () => {
                const points = score(input.value);
                const level = points <= 1 ? 0 : points === 2 ? 1 : 2; // weak, medium, strong
                meter.classList.toggle('hidden', input.value === '');
                meter.dataset.level = level;
                meter.querySelector('small').textContent = `${text.strength}: ${text.levels[level]}`;
            });
        });

        // after pressing send, the button says "Sending…" and can't be pressed again
        // (forms that update in place, and forms that stopped to ask "are you sure?", are left alone)
        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (form.method.toLowerCase() !== 'post' || form.matches('[data-soft]')) return;
            setTimeout(() => {
                if (e.defaultPrevented) return;
                form.querySelectorAll('button[type=submit], button:not([type])').forEach((button) => {
                    button.disabled = true;
                    button.setAttribute('aria-busy', 'true');
                    if (!button.querySelector('svg')) {
                        button.dataset.label = button.textContent;
                        button.textContent = text.sending;
                    }
                });
            });
        });

        // coming back with the browser's Back button brings the buttons back too
        window.addEventListener('pageshow', () => {
            document.querySelectorAll('button[aria-busy=true]').forEach((button) => {
                button.disabled = false;
                button.removeAttribute('aria-busy');
                if (button.dataset.label) button.textContent = button.dataset.label;
            });
        });

        // warn before leaving the page with a quote you wrote but did not send
        let sending = false;
        document.addEventListener('submit', (e) => { if (!e.target.matches('[data-soft]')) sending = true; });
        window.addEventListener('beforeunload', (e) => {
            if (sending) return;
            const unsent = [...document.querySelectorAll('form[method=POST] textarea[name=content]')]
                .some((box) => box.value.trim() !== '' && box.value !== box.defaultValue);
            if (unsent) e.preventDefault();
        });
    })();
</script>
