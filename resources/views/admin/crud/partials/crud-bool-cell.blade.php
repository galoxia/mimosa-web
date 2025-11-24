@php
    $value = $config['value'];
@endphp
<x-badge :variant="$value ? 'success' : 'danger'">
    {{ $value ? 'Sí' : 'No' }}
</x-badge>
