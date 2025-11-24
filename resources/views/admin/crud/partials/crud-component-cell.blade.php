<x-dynamic-component
    :component="$config['component']"
    :value="$config['field_value']"
    :attributes="new \Illuminate\View\ComponentAttributeBag($config['attributes'] ?? [])"
/>
