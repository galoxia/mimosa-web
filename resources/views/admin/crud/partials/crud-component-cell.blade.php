<x-dynamic-component
    :component="$config['component']"
    :value="$config['value']"
    :attributes="new \Illuminate\View\ComponentAttributeBag($config['attributes'] ?? [])"
/>
