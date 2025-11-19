<div
    x-data
>
    <div class="flex gap-6 mb-2">
        <span class="flex items-center gap-2">
            <!--suppress CssUnresolvedCustomProperty -->
            <span class="calendar-legend-color" style="background-color: var(--fc-today-bg-color);"></span> Hoy
        </span>
        <span class="flex items-center gap-2">
            <span class="calendar-legend-color fc-day-bookable"></span> Día abierto
        </span>
        <span class="flex items-center gap-2">
            <span class="calendar-legend-color fc-afternoon-full"></span> Tarde completa
        </span>
        <span class="flex items-center gap-2">
            <span class="calendar-legend-color fc-morning-full fc-afternoon-full"></span> Día completo
        </span>
        <span class="flex items-center gap-2 font-bold">
            <span class="calendar-legend-color bg-reserved block"></span> Mi cita
        </span>
    </div>

    <div id="calendar-{{ $calendar->id }}" class="calendar"></div>

    <div class="sliding-aside sliding-aside--right" :class="$store.ui.showAside && 'shown'">
        <div class="p-4">
            <div class="text-right">
                <button
                    type="button"
                    @click="$store.ui.showAside = false"
                    aria-label="Cerrar horarios"
                >
                    <x-heroicon-o-x-mark class="w-6 h-6 inline"/>
                </button>
            </div>

            <livewire:show-schedule/>
        </div>
    </div>
</div>

<script>
    // document.addEventListener( 'DOMContentLoaded', () => {
    document.addEventListener( 'FullCalendar:ready', () => {
        const { Calendar, dayGridPlugin, interactionPlugin, multiMonthPlugin, iCalendarPlugin } = FullCalendar;
        const config = @json($calendar->JSONize());

        const addDaysToDate = ( date, days ) => {
            date = date instanceof Date ? date : new Date( date );
            date.setDate( date.getDate() + days );
            return date.toLocaleDateString( 'sv-SE' );
        };

        const maxDate = ( date, other ) => {
            date = date instanceof Date ? date.toLocaleDateString( 'sv-SE' ) : date;
            other = other instanceof Date ? other.toLocaleDateString( 'sv-SE' ) : other;

            return date > other ? date : other;
        };

        const lastDayOfMonth = ( date ) => {
            date = date instanceof Date ? date : new Date( date );

            return new Date( date.getFullYear(), date.getMonth() + 1, 0 ).toLocaleDateString( 'sv-SE' );
        };

        const { start_date, end_date, actual_closing_date, id } = config;

        const $calendar = $( `#calendar-${ id }` );

        const visibleStart = maxDate( start_date, new Date() ).slice( 0, 8 ) + '01';
        const visibleEnd = addDaysToDate( lastDayOfMonth( actual_closing_date ), 1 );

        const options = {
            plugins: [
                dayGridPlugin,
                interactionPlugin,
                multiMonthPlugin,
                iCalendarPlugin,
            ],
            initialView: 'multiMonth',
            locale: 'es',
            firstDay: 1,
            multiMonthMaxColumns: 3,
            multiMonthMinWidth: 120,
            dateClick: ( info ) => {
                const schedule = config[ 'schedules' ][ info.date.toLocaleDateString( 'sv-SE' ) ];
                // En dateClick no viene info.isOther, miramos si existe la clase en el td.
                if ( info.dayEl.classList.contains( 'fc-day-other' ) || !schedule[ 'isBookable' ] ) {
                    return;
                }

                Alpine.store( 'ui' ).showAside = true;
                // Lanzar el evento al componente ShowSchedule para mostrar el horario del día seleccionado.
                Livewire.dispatchTo( 'show-schedule', 'date-click', { calendar_id: config.id, date: info.date.toLocaleDateString( 'sv-SE' ) } );
            },
            dayCellClassNames: function ( info ) {
                const schedule = config[ 'schedules' ][ info.date.toLocaleDateString( 'sv-SE' ) ];
                let classes = [];

                if ( schedule[ 'isHoliday' ] ) {
                    classes.push( 'fc-day-holiday' );
                }

                if ( !schedule[ 'isBookable' ] ) {
                    classes.push( 'fc-day-not-bookable' );
                } else if ( !info.isOther ) {
                    classes.push( 'fc-day-bookable' );
                    // Si todas las citas de un grupo (mañana o tarde) están reservadas, añadimos la clase correspondiente.
                    [ 'morning', 'afternoon' ].forEach( ( group ) => {
                        if ( schedule[ `is${ group }Full` ] ) {
                            classes.push( `fc-${ group }-full` );
                        }
                    } );
                    // Si el usuario tiene citas reservadas en el día, añadimos la clase correspondiente.
                    if ( schedule[ 'hasOwn' ] ) {
                        classes.push( 'has-own' );
                    }
                }

                return classes;
            },
            visibleRange: {
                start: visibleStart,
                end: visibleEnd,
            },
            headerToolbar: false,
            contentHeight: 'auto',
        };

        const calendar = new Calendar( $calendar[ 0 ], options );
        calendar.render();

        Livewire.on( 'book', ( { schedules } ) => {
            config.schedules = schedules;
            calendar.render();
        } );

        Livewire.on( 'unbook', ( { schedules } ) => {
            config.schedules = schedules;
            calendar.render();
        } );

        document.addEventListener( 'click', ( e ) => {
            const slider = $calendar[ 0 ].nextElementSibling;
            // Si pulsamos fuera del slider o fuera de un día abierto del calendario, cerramos el slider.
            if ( !slider.contains( e.target ) && !e.target.closest( '.fc-day-bookable' ) ) {
                Alpine.store( 'ui' ).showAside = false;
            }
        } );
    } );

    document.addEventListener( 'alpine:init', () => {
        Alpine.store( 'ui', {
            showAside: false
        } );
    } );
</script>
