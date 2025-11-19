export default function initCalendar() {
    if ( !document.querySelector( '.calendar' ) ) return;

    ( async () => {
        const [
            { Calendar },
            { default: dayGridPlugin },
            { default: interactionPlugin },
            { default: multiMonthPlugin },
            { default: iCalendarPlugin }
        ] = await Promise.all( [
            import('@fullcalendar/core'),
            import('@fullcalendar/daygrid'),
            import('@fullcalendar/interaction'),
            import('@fullcalendar/multimonth'),
            import('@fullcalendar/icalendar'),
        ] );

        window.FullCalendar = {
            Calendar,
            dayGridPlugin,
            interactionPlugin,
            multiMonthPlugin,
            iCalendarPlugin
        };

        console.log( '✅ FullCalendar cargado correctamente' );
        document.dispatchEvent( new CustomEvent( 'FullCalendar:ready' ) );
    } )();
}
