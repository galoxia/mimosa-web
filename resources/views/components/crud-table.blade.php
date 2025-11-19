@props([
    'table',
    'redirect_url' => null,
])

@php extract($table) @endphp
<div class="js-wrapper bg-white dark:bg-gray-800 rounded-lg shadow-md px-4 pt-6 pb-2 mb-6 relative max-h-[75vh] min-h-[25vh] overflow-y-hidden">
    <x-spinner size="24" text="Cargando datos..." class="z-30"/>

    <table id="entities" class="display js-datatable">
        <thead class="text-base">
        <tr>
            @foreach($headers as $field => $label)
                <th>{{ $label ?? $field }}</th>
            @endforeach
            <th class="js-dt-actions">Acciones</th>
        </tr>
        </thead>
        <tbody class="text-base">
        @foreach($rows as $row)
            @php extract($row) @endphp
            <tr>
                @foreach($fields as $field => $config)
                    @php
                        $value = $config['value'];
                    @endphp
                    @if(isset($config['component']))
                        <x-dynamic-component :component="$config['component']" :value="$value" :attributes="new \Illuminate\View\ComponentAttributeBag($config['attributes'] ?? [])"/>
                    @else
                        @switch($config['type'] ?? 'text')
                            @case('bool')
                                <td>
                                    <x-badge :variant="$value ? 'success' : 'danger'">
                                        {{ $value ? 'Sí' : 'No' }}
                                    </x-badge>
                                </td>
                                @break
                            @case('relation')
                                {{-- $value es un modelo cuando el tipo es una relación --}}
                                <td>
                                    @if($value)
                                        <x-link :href="route('admin.crud.get', ['action' => 'update', 'model' => $value::class, 'id' => $value->id])">
                                            {{ $value }}
                                        </x-link>
                                    @endif
                                </td>
                                @break
                            @default
                                <td>{{ $value }}</td>
                        @endswitch
                    @endif
                @endforeach
                <td class="actions-col">
                    <div class="flex justify-end items-stretch gap-2.5">
                        @includeIf("admin.crud.$slug.actions-col")

                        @if($entity->isUpdatable())
                            <x-button-link
                                :href="route('admin.crud.get', array_filter(['action' => 'update', 'id' => $entity->id, 'model' => $model, 'redirect_url' => $redirect_url]))"
                                icon="pencil"
                                variant="info"
                                :outline="true"
                                title="Editar"
                                aria-label="Editar"
                            ></x-button-link>
                        @endif

                        @if($entity->isDeletable())
                            <form action="{{ route('admin.crud.delete') }}" method="post" class="js-confirm">
                                @csrf
                                @method('DELETE')

                                <input type="hidden" name="model" value="{{ $model }}">
                                <input type="hidden" name="id" value="{{ $entity->id }}">
                                @if($redirect_url)
                                    <input type="hidden" name="redirect_url" value="{{ $redirect_url }}">
                                @endif

                                <x-button type="submit" variant="danger" icon="trash" title="Borrar" :outline="true" aria-label="Borrar"></x-button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
