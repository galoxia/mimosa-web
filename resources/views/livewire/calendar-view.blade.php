<div>
    @foreach($calendars as $calendar)
        <div class="mb-2.5 mt-8 text-xl border-b font-medium pb-2 text-purple-700 border-purple-700 dark:border-purple-400 dark:text-purple-400">
            {{ $calendar->workshop->name }}
        </div>

        <div class="flex gap-6 mb-2.5">
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

        <div wire:ignore id="calendar-{{ $calendar->id }}" class="calendar"></div>
    @endforeach

    <div
        x-data="{ shown: false }"
        x-ref="schedules"
        class="sliding-aside sliding-aside--right"
        :class="{ shown }"
        @click.window="shown = $refs.schedules.contains( $event.target ) || $event.target.closest( '.fc-day-bookable' )"
        @openSchedule.window="shown = true"
    >
        <div class="p-4">
            <div class="text-right">
                <button type="button" @click="shown = false" aria-label="Cerrar horarios">
                    <x-heroicon-o-x-mark class="w-6 h-6 inline"/>
                </button>
            </div>

            <livewire:show-schedule/>
        </div>
    </div>
</div>

<script>
    document.addEventListener( 'FullCalendar:ready', () => {
        const { Calendar, dayGridPlugin, interactionPlugin, multiMonthPlugin, iCalendarPlugin } = FullCalendar;

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

        const calendars = {};
        @foreach($calendars as $calendar)
            console.log( @json($calendar->id) );
            calendars[@json($calendar->id)] = { calendar: null, config: @json($calendar->JSONize()) };
        @endforeach

        Object.entries( calendars ).forEach( ( [ id, { config } ] ) => {
            const { start_date, end_date, actual_closing_date } = config;
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
                    const config = calendars[ id ].config;
                    const schedule = config[ 'schedules' ][ info.date.toLocaleDateString( 'sv-SE' ) ];
                    // En dateClick no viene info.isOther, miramos si existe la clase en el td.
                    if ( info.dayEl.classList.contains( 'fc-day-other' ) || !schedule[ 'isBookable' ] ) {
                        return;
                    }

                    document.dispatchEvent( new CustomEvent( 'openSchedule' ) );
                    // Lanzar el evento al componente ShowSchedule para mostrar el horario del día seleccionado.
                    Livewire.dispatchTo( 'show-schedule', 'date-click', { calendar_id: config.id, date: info.date.toLocaleDateString( 'sv-SE' ) } );
                },
                dayCellClassNames: function ( info ) {
                    const config = calendars[ id ].config;
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
            calendars[ id ].calendar = calendar;
            calendar.render();
        } );

        Livewire.on( 'book', ( { calendar_id, schedules } ) => {
            calendars[ calendar_id ].config.schedules = schedules;
            calendars[ calendar_id ].calendar.render();
        } );

        Livewire.on( 'unbook', ( { calendar_id, schedules } ) => {
            calendars[ calendar_id ].config.schedules = schedules;
            calendars[ calendar_id ].calendar.render();
        } );
    } );
</script>

