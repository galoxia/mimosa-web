@php $messageOptions = \App\Models\Message::options(); @endphp
@if($messageOptions)
    <div
        x-data="appointmentsSend"
        x-cloak
        class="relative"
    >
        <x-button
            icon="envelope"
            variant="purple"
            :outline="true"
            @click="open()"
            aria-label="Enviar mensaje/sms a {{ $row['entity']->user->public_name }}"
            title="Enviar..."
        ></x-button>

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
            <div class="overlay-content bg-white dark:bg-gray-800 p-4 rounded-lg shadow-lg">
                <div class="flex flex-col xl:flex-row justify-between items-center gap-4">
                    <div class="card">
                        <form
                            action="{{ route('admin.crud.users.send', ['user' => $row['entity']->user->id]) }}"
                            method="post"
                            class="js-confirm"
                        >
                            @csrf

                            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Enviar mensaje</h5>
                            <p class="mb-3 font-normal text-muted">
                                Envía un email de la plantilla seleccionada a {{ $row['entity']->user->public_name }}.
                            </p>

                            <select name="message_id" id="message_id" class="form-select mb-3">
                                @foreach($messageOptions as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>

                            <x-button
                                icon="envelope"
                                variant="success"
                                class="w-full text-lg"
                            >
                                Enviar email
                            </x-button>
                        </form>
                    </div>

{{--                    <div class="card">--}}
{{--                        <form--}}
{{--                            action="{{ route('admin.crud.users.sendSMS', ['user' => $row['entity']->user->id]) }}"--}}
{{--                            method="post"--}}
{{--                            class="js-confirm"--}}
{{--                        >--}}
{{--                            @csrf--}}

{{--                            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Enviar SMS</h5>--}}
{{--                            <p class="mb-3 font-normal text-muted">--}}
{{--                                Envía un SMS de la plantilla seleccionada a {{ $row['entity']->user->public_name }}.--}}
{{--                            </p>--}}

{{--                            <select name="message_id" id="message_id" class="form-select mb-3">--}}
{{--                                @foreach($messageOptions as $id => $name)--}}
{{--                                    <option value="{{ $id }}">{{ $name }}</option>--}}
{{--                                @endforeach--}}
{{--                            </select>--}}

{{--                            <x-button--}}
{{--                                icon="chat-bubble-left-ellipsis"--}}
{{--                                variant="success"--}}
{{--                                class="w-full text-lg"--}}
{{--                            >--}}
{{--                                Enviar SMS--}}
{{--                            </x-button>--}}
{{--                        </form>--}}
{{--                    </div>--}}
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener( 'alpine:init', () => {
            Alpine.data( 'appointmentsSend', () => {
                return {
                    isOpen: false,
                    isVisible: false,

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
                    }
                };
            } );
        } );
    </script>
@endif
