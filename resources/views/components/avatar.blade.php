@props(['user', 'size' => 'h-9 w-9 text-sm'])

{{-- the user's picture, or the first letter of their name if they did not upload one --}}
@if ($user->avatarUrl())
    <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" {{ $attributes->merge(['class' => "$size shrink-0 rounded-full object-cover"]) }}>
@else
    <span {{ $attributes->merge(['class' => "$size flex shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-accent to-sand font-extrabold text-white"]) }}>{{ mb_substr($user->name, 0, 1) }}</span>
@endif
