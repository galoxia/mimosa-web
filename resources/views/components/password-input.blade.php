@props([
    'type' => 'password', // Lo "sacamos" del bag de atributos. Su valor es fijo en el input ("password").
    'id' => null,
    'name' => 'password',
])

@php
    $class = 'form-control form-input pe-10';
    $id ??= str_replace('_', '-', $name);
    $autocomplete = 'current-password';
    $required = true;
@endphp

<div class="relative" x-data="{ show: false }">
    <input
        type="password"
        :type="show ? 'text' : 'password'"
        {{ $attributes->merge(compact('class', 'name', 'id', 'autocomplete', 'required')) }}
    >

    <button
        type="button"
        class="absolute right-2 top-1/2 -translate-y-1/2 w-[1.3em] h-[1.3em]"
        @click="show = !show"
        :aria-label="show ? '{{ __('Ocultar contraseña') }}' : '{{ __('Mostrar contraseña') }}'"
        :aria-pressed="show.toString()"
    >
        <x-dynamic-component x-show="!show" component="heroicon-o-eye"/>
        <x-dynamic-component x-cloak x-show="show" component="heroicon-o-eye-slash"/>
    </button>
</div>


