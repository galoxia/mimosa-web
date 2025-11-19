@props([
    'variant' => 'info',
    'size' => 'sm',
])

<span
    @class([
        "text-$size font-medium me-2 px-2.5 py-0.5 rounded-sm",
        'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' => $variant === 'info',
        'bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-300' => $variant === 'primary',
        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' => $variant === 'success',
        'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' => $variant === 'warning',
        'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' => $variant === 'danger',
    ])
>
    {{ $slot }}
</span>
