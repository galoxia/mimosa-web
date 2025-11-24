@php
    $entity = $config['field_value'];
    $value = $config['value'];
@endphp
{{-- $fieldValue es un modelo cuando el tipo es una relación --}}
@if($entity)
    @if($entity->isUpdatable())
        <x-link :href="route('admin.crud.get', ['action' => 'update', 'model' => $entity::class, 'id' => $entity->id, 'redirect_url' => $redirect_url])">
            {!! $value !!}
        </x-link>
    @else
        {!! $value !!}
    @endif
@else
    {{--    <x-badge variant="warning">Nulo</x-badge>--}}
@endif

