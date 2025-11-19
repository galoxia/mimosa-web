@props([
    'field',
    'config',
    'index' => 0,
])

@if($config['values']['options'] !== null)
    <select
        name="{{ $field }}[values][]"
        class="form-control form-select mt-2 first:mt-0"
        @disabled($config['values']['options'] === [])
    >
        @foreach($config['values']['options'] as $value => $label)
            <option value="{{ $value }}" @selected($value == ( $config['values']['current'][$index] ?? null ))>
                {{ $label }}
            </option>
        @endforeach
    </select>
@else
    <input name="{{ $field }}[values][]" type="{{ $config['type'] ?? 'text' }}" class="form-control form-input mt-2 first:mt-0" value="{{ $config['values']['current'][$index] ?? '' }}">
@endif
