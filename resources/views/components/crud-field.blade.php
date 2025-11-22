@props([
    'field',
    'config',
    'value' => null
])

@php
    $component = $config['component'] ?? null;
    $attributes = $config['attributes'] ?? [];
    $type = $config['type'] ?? 'text';
    $required = $config['required'] ?? true;
    $name = $config['name'] ?? $field;
@endphp

@if($config['label'] ?? '')
    <label for="{{ $name }}" class="form-label {{ $config['label_class'] ?? '' }}">
        {{ $config['label'] }}
    </label>
@endif

@if($component)
    <x-dynamic-component
        :component="$component"
        :attributes="new \Illuminate\View\ComponentAttributeBag( array_merge( $attributes, compact( 'name', 'type', 'required', 'value' ) ) )"
    />
@else
    @php
        $attributes = implode( ' ', array_map( fn( $attribute, $value ) => "$attribute=$value", array_keys( $attributes ), $attributes ) );
        $isMultiple = isset($config['attributes']['multiple']);
        // Filtra nulos pero no valores "false"
        $values = array_filter( is_array( $value ) ? $value : [ $value ], fn( $value ) => !is_null( $value ) );
    @endphp

    @switch($type)
        @case('select')
            <select
                id="{{ $name }}" name="{{ sprintf('%s%s', $name, $isMultiple ? '[]' : '') }}"
                class="form-control form-select {{ $config['class'] ?? '' }}"
                {!! $attributes !!}
                @required($required)
            >
                @if($config['placeholder'] ?? false)
                    <option value="" hidden>{{ $config['placeholder'] }}</option>
                @endif

                @foreach ($config['options'] ?? [] as $id => $label)
                    <option value="{{ $id }}" {{ in_array((string) $id, $values) ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @break
        @case('textarea')
            <textarea
                id="{{ $name }}" name="{{ $name }}"
                class="form-control form-textarea {{ $config['class'] ?? '' }}"
                {{ $attributes }}
                placeholder="{{ $config['placeholder'] ?? '' }}"
                @required($required)
            >{{ $value }}</textarea>
            @break
        @case('file')
            <input
                type="file" id="{{ $name }}" name="{{ sprintf('%s%s', $name, $isMultiple ? '[]' : '') }}"
                class="form-control form-input {{ $config['class'] ?? '' }}"
                {{ $attributes }}
                @required($required)
            >
            @if($values)
                <p class="text-muted text-sm mt-2">{{ implode(', ', $values) }}</p>
            @endif
            @break
        @default
            <input
                type="{{ $type }}" id="{{ $name }}" name="{{ $name }}" value="{{ $value }}"
                class="form-control form-input {{ $config['class'] ?? '' }}"
                {{ $attributes }}
                autocomplete="{{ $config['autocomplete'] ?? 'off' }}"
                placeholder="{{ $config['placeholder'] ?? '' }}"
                @required($required)
            >
    @endswitch
@endif

@php $footer = $config['footer'] ?? false @endphp
@if(is_array($footer))
    <p class="crud-field-footer {{ $footer['class'] ?? 'text-sm mt-2' }}">{{ $footer['text'] ?? '' }}</p>
@elseif($footer === true)
    <p class="crud-field-footer text-muted text-sm mt-2"></p>
@elseif($footer)
    {!! $footer !!}
@endif

@error($name)
<p class="validation-error mt-2">{{ $message }}</p>
@enderror

@if ($errors->has("$name.*"))
    <p class="validation-error mt-2">{{ $errors->first("$name.*") }}</p>
@endif

