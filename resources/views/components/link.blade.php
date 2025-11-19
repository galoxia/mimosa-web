@props([
    'href'
])

<a
    href="{{ $href }}"
    {{ $attributes->merge([
         'class' => 'link-primary font-semibold hover:underline'
    ]) }}
>{{ $slot }}</a>
