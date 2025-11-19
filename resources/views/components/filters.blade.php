<div
    x-data="filters"
    x-cloak
    x-show="isReady"
>
    <x-button
        icon="adjustments-horizontal"
        variant="light"
        @click="open()"
    >
        Filtros
        <span class="text-info" x-show="enabled.length">(<span x-text="enabled.length"></span>)</span>
    </x-button>

    <div
        :class="{
            'overlay fixed inset-0 bg-black/60 z-30 grid place-items-center p-4': true,
            'is-visible': isVisible,
            'is-open': isOpen,
        }"
        x-show="isOpen"
        x-transition.opacity.duration.300ms
        @transitionstart="isVisible = true"
        @transitionend="onTransitionEnd()"
        @click="onClickOverlay($event)"
        @keydown.escape.window="close()"
    >
        <div class="overlay-content bg-white dark:bg-gray-800 ps-2 pe-4 py-4 rounded-lg shadow-lg">
            <form
                action="{{ route('admin.crud.post', ['action' => 'filter', 'model' => request()->query('model')]) }}"
                method="post"
                @submit.prevent="submit($event)"
                class="max-h-[80vh] overflow-y-auto px-2"
            >
                @csrf
                <header class="flex top-0 justify-between items-stretch mb-8 gap-8 pb-4 pt-2 sticky top-0 z-10 bg-white dark:bg-gray-800">
                    <x-button type="button" variant="danger" icon="trash" @click="clear()">
                        Borrar
                    </x-button>

                    <span class="text-lg font-semibold flex items-center gap-2">
                        <x-dynamic-component component="heroicon-o-funnel" class="w-[1.1em] h-[1.1em]"/>
                        Filtros
                    </span>

                    <x-button variant="success" icon="check" type="submit">
                        Aplicar
                    </x-button>
                </header>

                <ul class="list-none text-lg">
                    @foreach($filters as $field => $config)
                        <li class="flex flex-col mb-4 border-b pb-4 last:border-none last:mb-0 last:pb-0 border-gray-200">
                            <div class="mb-2 ps-1">
                                <input
                                    type="checkbox"
                                    name="{{ $field }}[enabled]"
                                    class="scale-125 me-2"
                                    value="{{ $field }}"
                                    x-model="enabled"
                                    @disabled($config['values']['options'] === [])
                                >
                                <span>{{ $config['label'] }}</span>
                            </div>
                            <div class="mb-2">
                                <select
                                    name="{{ $field }}[operator]"
                                    class="form-control form-select"
                                    x-model="operators['{{ $field }}']"
                                    @disabled($config['values']['options'] === [])
                                >
                                    @foreach($config['operators']['options'] as $id => $label)
                                        <option value="{{ $id }}" @selected($id === $config['operators']['selected'])>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-filter-value :config="$config" :field="$field"/>

                                <template x-if="operators['{{ $field }}'].includes('between')">
                                    <x-filter-value :config="$config" :field="$field" index="1"/>
                                </template>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener( 'alpine:init', () => {
        Alpine.data( 'filters', () => {
            let filters = @json($filters);

            return {
                isReady: false,
                isOpen: false,
                isVisible: false,
                enabled: [],
                operators: {},

                init() {
                    filters = Object.entries( filters );

                    filters.forEach( ( [ field, { operators, enabled } ] ) => {
                        this.operators[ field ] = operators.selected || Object.keys( operators.options )[ 0 ];
                        if ( enabled ) {
                            this.enabled.push( field );
                        }
                    } );

                    this.isReady = filters.length > 0;
                },

                open() {
                    this.isOpen = true;

                    const scrollBarWidth = window.innerWidth - document.documentElement.clientWidth;
                    document.body.style.overflow = 'hidden';
                    document.body.style.paddingRight = `${ scrollBarWidth }px`;
                },

                close() {
                    this.isOpen = false;
                },

                onClickOverlay( event ) {
                    if ( !event.target.closest( '.overlay-content' ) ) {
                        this.close();
                    }
                },

                onTransitionEnd() {
                    this.isVisible = this.isOpen;

                    if ( !this.isVisible ) {
                        document.body.style.overflow = '';
                        document.body.style.paddingRight = '';
                    }
                },

                clear() {
                    this.enabled = [];
                },

                submit( event ) {
                    event.target.submit();
                },
            };
        } );
    } );
</script>
