@props([
    'table',
    'ajax' => false
])

@php extract($table) @endphp
<div class="js-wrapper bg-white dark:bg-gray-800 rounded-lg shadow-md px-4 pt-6 pb-2 mb-6 relative min-h-[25vh]">
    <x-spinner size="24" text="Cargando datos..." class="z-30"/>

    <table
        data-column-defs="{{ $table['columnDefs'] }}"
        @if($ajax)
            data-model="{{ $table['model'] }}" data-ajax-url="{{ route('admin.crud.datatable') }}"
        @endif
        @class([
            'display',
            'js-datatable' => !$ajax,
            'js-ajax-datatable' => $ajax,
        ])
    >
        <thead class="text-base">
        <tr>
            @foreach($headers as $field => $label)
                <th>{{ $label ?? $field }}</th>
            @endforeach
{{--            <th class="js-dt-actions">Acciones</th>--}}
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody class="text-base">
        @if(!$ajax)
            @foreach($rows as $row)
                <tr>
                    @foreach($row as $value)
                        <td>
                            {!! $value !!}
                        </td>
                    @endforeach
                </tr>
            @endforeach
        @endif
        </tbody>
    </table>
</div>
