@props([
    'href',
    'icon' => null,
])

@php
    $isActive = request()->uri()->value() === url( $href );
@endphp

<a
    href="{{ $href }}"
    {{ $attributes->merge([
        'class' => ($isActive ? 'is-active' : '') . ' admin-link'
    ]) }}
>
    @if($icon)
        {{-- Heroicon outline --}}
        <x-dynamic-component :component="'heroicon-o-' . $icon" class="w-[1.1em] h-[1.1em]"/>
    @endif

    @if($slot->isNotEmpty())
        <span>{{ $slot }}</span>
    @endif
</a>
