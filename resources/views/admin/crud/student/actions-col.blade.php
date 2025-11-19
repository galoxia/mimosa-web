<x-button-link
    :href="route('impersonate', $row['entity']->user->id)"
    icon="user"
    variant="purple"
    :outline="true"
    aria-label="Suplantar"
    title="Suplantar"
>
</x-button-link>
