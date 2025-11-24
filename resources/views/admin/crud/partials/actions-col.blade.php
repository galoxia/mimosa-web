@php
    $model = $entity::class;
    $slug = $model::getClassSlug();
@endphp

<div class="flex justify-end items-stretch gap-2.5">
    @includeIf("admin.crud.$slug.partials.actions-col")

    @if($entity->isUpdatable())
        <x-button-link
            :href="route('admin.crud.get', array_filter(['action' => 'update', 'id' => $entity->id, 'model' => $model, 'redirect_url' => $redirect_url]))"
            icon="pencil"
            variant="info"
            :outline="true"
            title="Editar"
            aria-label="Editar"
        ></x-button-link>
    @endif

    @if($entity->isDeletable())
        <form action="{{ route('admin.crud.delete') }}" method="post" class="js-confirm">
            @csrf
            @method('DELETE')

            <input type="hidden" name="model" value="{{ $model }}">
            <input type="hidden" name="id" value="{{ $entity->id }}">
            @if($redirect_url)
                <input type="hidden" name="redirect_url" value="{{ $redirect_url }}">
            @endif

            <x-button type="submit" variant="danger" icon="trash" title="Borrar" :outline="true" aria-label="Borrar"></x-button>
        </form>
    @endif
</div>
