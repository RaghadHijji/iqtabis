{{-- An eye button on every password field: press it to see what you typed, press again to hide it --}}
<script>
    (() => {
        const eye = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>';
        const eyeOff = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 3l18 18M10.6 5.1A10 10 0 0 1 12 5c6.4 0 10 7 10 7a17 17 0 0 1-3.2 4.1M6.6 6.6C3.9 8.4 2 12 2 12s3.6 7 10 7a9.7 9.7 0 0 0 5.4-1.6M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>';
        const show = @json(__('Show password'));
        const hide = @json(__('Hide password'));

        document.querySelectorAll('.auth-field input[type=password]').forEach((input) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'pw-toggle';
            button.innerHTML = eye;
            button.title = button.ariaLabel = show;
            // the eye takes the place of the lock icon
            input.parentElement.querySelector(':scope > svg')?.remove();
            input.parentElement.append(button); // after the label text, so the floating label still works

            button.addEventListener('click', () => {
                const hidden = input.type === 'password';
                input.type = hidden ? 'text' : 'password';
                button.innerHTML = hidden ? eyeOff : eye;
                button.title = button.ariaLabel = hidden ? hide : show;
                input.focus();
            });
        });
    })();
</script>
