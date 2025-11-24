@props([
    'table',
    'foreign_key' => null,
    'foreign_id' => null,
])

@php extract($table) @endphp
<div class="js-wrapper bg-white dark:bg-gray-800 rounded-lg shadow-md px-4 pt-6 pb-2 mb-6 relative max-h-[75vh] min-h-[25vh] overflow-y-hidden">
    <x-spinner size="24" text="Cargando datos..." class="z-30"/>

    <table
        data-column-defs="{{ $table['columnDefs'] }}"
        data-model="{{ $table['model'] }}"
        data-ajax-url="{{ route('admin.crud.datatable') }}"
        class="display js-ajax-datatable"
        data-foreign-key="{{ $foreign_key }}"
        data-foreign-id="{{ $foreign_id }}"
    >
        <thead class="text-base">
        <tr>
            @foreach($headers as $field => $label)
                <th>{{ $label ?? $field }}</th>
            @endforeach
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody class="text-base"></tbody>
    </table>
</div>
