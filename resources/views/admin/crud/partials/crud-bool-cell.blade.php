@php
    $value = $config['field_value'];
@endphp
<x-badge :variant="$value ? 'success' : 'danger'">
    {{ $value ? 'Sí' : 'No' }}
</x-badge>
