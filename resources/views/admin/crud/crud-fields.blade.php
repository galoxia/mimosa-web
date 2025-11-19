@csrf

<input type="hidden" name="model" value="{{ $entity::class }}">
<input type="hidden" name="redirect_url" value="{!! session('redirect_url', request()->query('redirect_url')) !!}">

@php
    $sections = $fields['_all']['sections'] ?? 1;
    $cols = explode(',', $fields['_all']['cols'] ?? 1);
@endphp

<div class="flex flex-col xl:flex-row gap-12 mb-12">
    @for ($i = 0; $i < $sections; $i++)
        <div class="w-full max-w-lg grid place-content-start grid-cols-{{ $cols[$i] ?? 1 }} gap-{{ $fields['_all']['gap'] ?? 4 }}">
            @php $sectionFields = array_filter($fields, fn($config, $field) => $field !== '_all' && ($config['section'] ?? 0) === $i, ARRAY_FILTER_USE_BOTH); @endphp

            @hasSection("begin-crud-section-$i")
                @yield("begin-crud-section-$i")
            @endif

            @foreach($sectionFields as $field => $config)
                @hasSection("before-crud-field-$field")
                    @yield("before-crud-field-$field")
                @endif

                <div class="col-span-{{ $config['cols'] ?? $cols[$i] ?? 1 }}">
                    <x-crud-field :field="$field" :config="$config" :value="old($field, $config['value'])"/>
                </div>

                @hasSection("after-crud-field-$field")
                    @yield("after-crud-field-$field")
                @endif
            @endforeach

            @hasSection("end-crud-section-$i")
                @yield("end-crud-section-$i")
            @endif
        </div>
    @endfor
</div>
