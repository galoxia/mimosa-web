@props([
    'href',
    'variant' => 'primary',
    'icon' => null,
    'outline' => false,
])

<a
    href="{{ $href }}"
    {{ $attributes->class([
        'inline-flex items-center justify-center gap-2',
        'btn' => !$outline,
        "btn-$variant" => !$outline,
        'btn-outline' => $outline,
        "btn-$variant-outline" => $outline,
    ]) }}
>
    @if($icon)
        <x-dynamic-component :component="'heroicon-o-' . $icon" class="w-[1.1em] h-[1.1em]"/>
    @endif

    @if($slot->isNotEmpty())
        <span>{{ $slot }}</span>
    @endif
</a>
