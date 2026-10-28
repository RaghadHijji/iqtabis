@props(['href' => route('home')])

{{-- "Back": returns to the page you came from on this site; if you came from outside, it opens :href instead --}}
<a href="{{ $href }}" data-back {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 font-bold text-accent hover:underline']) }}>
    <x-icon name="arrow" class="h-4 w-4 ltr:rotate-180" />{{ __('Back') }}
</a>

@once
    <script>
        document.addEventListener('click', (event) => {
            const link = event.target.closest('[data-back]');
            if (link && document.referrer.startsWith(location.origin) && history.length > 1) {
                event.preventDefault();
                history.back();
            }
        });
    </script>
@endonce
