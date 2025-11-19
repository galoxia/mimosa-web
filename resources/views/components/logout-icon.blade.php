@props([
    'size' => 6
])

@php
    $manager = app('impersonate');
    $color = $manager->isImpersonating() ? 'purple' : 'red';

    $iconClasses = "w-$size h-$size inline text-$color-600 hover:text-$color-700 dark:text-$color-500 dark:hover:text-$color-400";
@endphp

@if($manager->isImpersonating())
    <a
        href="{{ route('impersonate.leave') }}"
        aria-label="Dejar de suplantar"
        title="Dejar de suplantar"
    >
        <x-heroicon-o-arrow-right-start-on-rectangle
            {{ $attributes->class([ $iconClasses => true ]) }}
        />
    </a>
@else
    <form action="{{ route('logout') }}" method="post">
        @csrf

        <button
            type="submit"
            aria-label="Cerrar sesión"
            title="Cerrar sesión"
        >
            <x-heroicon-o-arrow-right-start-on-rectangle
                {{ $attributes->class([ $iconClasses => true ]) }}
            />
        </button>
    </form>
@endif

