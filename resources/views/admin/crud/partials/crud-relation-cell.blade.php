@php
    $value = $config['value'];
@endphp
{{-- $value es un modelo cuando el tipo es una relación --}}
@if($value)
    @if($value->isUpdatable())
        <x-link :href="route('admin.crud.get', ['action' => 'update', 'model' => $value::class, 'id' => $value->id, 'redirect_url' => $redirect_url])">
            {{ $value }}
        </x-link>
    @else
        {{ $value }}
    @endif
@else
{{--    <x-badge variant="warning">Nulo</x-badge>--}}
@endif

