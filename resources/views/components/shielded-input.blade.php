@props([
    'id' => null,
])

@php
    $class = 'form-control form-input pe-10 disabled';
    $id ??= $attributes->get('name');
@endphp

<div class="relative" x-data="{ shield: true }">
    <input
        :readonly="shield"
        :class="{
            'disabled': shield
        }"
        {{ $attributes->merge(compact('class', 'id')) }}
    >

    <button
        type="button"
        class="absolute right-2 top-1/2 -translate-y-1/2 w-[1.3em] h-[1.3em]"
        @click="shield = !shield"
        :aria-label="shield ? '{{ __('Deshabilitar campo') }}' : '{{ __('Habilitar campo') }}'"
        :aria-pressed="shield.toString()"
    >
        <x-dynamic-component x-cloak x-show="!shield" component="heroicon-o-shield-check"/>
        <x-dynamic-component x-show="shield" component="heroicon-o-shield-exclamation"/>
    </button>
</div>


