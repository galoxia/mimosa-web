@props([
    'variant' => 'info',
    'dismissAfter' => 6000,
])

<div
    x-data="statusBar"
    x-on:status-message.window="onMessage($event.detail)"
    id="status-bar"
    class="status-bar translate-y-full"
    :class="{
        'translate-y-full': !open,
        'bg-sky-300 dark:bg-sky-600': variant === 'info',
        'bg-amber-300 dark:bg-amber-600': variant === 'warning',
        'bg-red-300 dark:bg-red-600': variant === 'danger',
    }"
>
    <p class="text-lg font-semibold" x-html="status"></p>
</div>

<script>
    document.addEventListener( 'alpine:init', () => {
        Alpine.data( 'statusBar', () => {
            let handler = 0;

            return {
                open: false,
                variant: '{{ $variant }}',
                dismissAfter: {{ $dismissAfter }},
                status: '{{ $slot }}',

                init() {
                    if ( this.status ) {
                        this.show();
                    }
                },

                onMessage( { status, variant = 'info', dismissAfter = 6000 } ) {
                    this.status = status;
                    this.variant = variant;
                    this.dismissAfter = dismissAfter;

                    this.show();
                },

                show() {
                    this.open = true;
                    clearTimeout( handler );
                    handler = setTimeout( () => this.hide(), this.dismissAfter );
                },

                hide() {
                    this.open = false;
                }
            };
        } );
    } );
</script>
