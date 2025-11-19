@props([
    'variant' => 'primary',
    'icon' => null,
    'disabled' => false,
    'outline' => false,
])

<button
    {{ $attributes->class([
        'inline-flex items-center justify-center gap-2',
        'btn' => !$outline,
        "btn-$variant" => !$outline,
        'btn-outline' => $outline,
        "btn-$variant-outline" => $outline,
    ]) }}
    @disabled($disabled)
>
    @if($icon)
        <x-dynamic-component :component="'heroicon-o-' . $icon" class="w-[1.1em] h-[1.1em]"/>
    @endif

    @if($slot->isNotEmpty())
        <span>{{ $slot }}</span>
    @endif
</button>
