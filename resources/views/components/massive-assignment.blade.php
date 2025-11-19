<div
    x-data="massive"
    x-show="ready"
    x-cloak
>
    <p class="text-muted mb-2">
        Antes de aplicar, <span class="text-danger font-semibold">elige los filtros necesarios</span> para delimitar la selección.
    </p>

    <form
        action="{{ route('admin.crud.post', ['action' => 'massive-update', 'model' => request()->query('model')]) }}"
        method="post"
        class="js-confirm text-sm inline-block"
    >
        @csrf

        <div class="flex justify-between items-stretch gap-4">
            <select
                name="field"
                class="form-control form-select text-sm"
                x-model="selected"
            >
                <template x-for="(config, field) in fields" :key="field">
                    <option :value="field" x-text="config.label"></option>
                </template>
            </select>

            <template x-if="selected && fields[selected].isSelect">
                <select name="value" class="form-control form-select" x-model="value">
                    <template x-for="(label, option) in fields[selected].options" :key="value">
                        <option :value="option" x-text="label"></option>
                    </template>
                </select>
            </template>

            <template x-if="selected && !fields[selected].isSelect">
                <input class="form-control form-input w-36" name="value" :type="fields[selected].type" x-model="value">
            </template>

            <x-button type="button" variant="success" icon="check" type="submit" x-bind:disabled="!value">
                Aplicar
            </x-button>
        </div>
    </form>
</div>

<script>
    document.addEventListener( 'alpine:init', () => {
        Alpine.data( 'massive', () => {
            return {
                fields: @json($fields),
                selected: null,
                value: null,
                ready: false,

                init() {
                    const keys = Object.keys( this.fields );

                    if ( keys.length > 0 ) {
                        this.selected = keys[ 0 ];
                        this.ready = true;
                    }
                },
            };
        } );
    } );
</script>

