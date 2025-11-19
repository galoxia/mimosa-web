@props([
    'variant' => 'info',
    'icon' => 'information-circle',
    'size' => 'lg',
])

<div
    role="alert"
    @class([
        "flex items-center p-4 text-$size rounded border dark:border-none",
        'text-primary-800 bg-primary-50 border-primary-800 dark:bg-gray-700 dark:text-primary-400' => $variant === 'primary',
        'text-blue-800 bg-blue-50 border-blue-800 dark:bg-gray-700 dark:text-blue-400' => $variant === 'info',
        'text-green-800 bg-green-50 border-green-800 dark:bg-gray-700 dark:text-green-400' => $variant === 'success',
        'text-yellow-800 bg-yellow-50 border-yellow-800 dark:bg-gray-700 dark:text-yellow-300' => $variant === 'warning',
        'text-red-800 bg-red-50 border-red-800 dark:bg-gray-700 dark:text-red-400' => $variant === 'danger',
    ])
>
    @if($icon)
        <x-dynamic-component :component="'heroicon-o-' . $icon" class="w-[1.4em] h-[1.4em] me-2 -mb-[0.1em]"/>
    @endif
    <p class="font-semibold">
        {{ $slot }}
    </p>
</div>
