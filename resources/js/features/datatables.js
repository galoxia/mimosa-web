export default function initDataTables() {
    // const $dataTables = $( '.js-datatable' );
    const $ajaxDataTables = $( '.js-ajax-datatable' );

    // if ( !$dataTables.length && !$ajaxDataTables.length ) return;
    if ( !$ajaxDataTables.length ) return;

    ( async () => {
        const [ { default: DataTable }, , , { default: language } ] = await Promise.all( [
            import( 'datatables.net-dt' ),
            import( 'datatables.net-responsive-dt' ),
            import( 'datatables.net-fixedcolumns-dt' ),
            import( 'datatables.net-plugins/i18n/es-ES.mjs' ),
        ] );

        await import('../datatables/dataTables.tailwindcss.js');

        console.log( '✅ DataTables cargado correctamente' );
        // En la paginación, dejamos los valores de la versión en inglés.
        delete language.paginate;

        // $dataTables.each( function ( _, element ) {
        //     new DataTable( element, {
        //         language,
        //         order: [],
        //         fixedColumns: {
        //             start: 1,
        //             end: 1,
        //         },
        //         scrollX: true,
        //         initComplete() {
        //             const $wrapper = $( element ).closest( '.js-wrapper' );
        //             $wrapper.removeClass( 'max-h-[75vh] min-h-[25vh] overflow-y-hidden' );
        //             $wrapper.children( '.spinner' ).addClass( 'hidden' );
        //         },
        //     } );
        // } );

        $ajaxDataTables.each( function ( _, element ) {
            const $wrapper = $( element ).closest( '.js-wrapper' );
            const $spinner = $wrapper.children( '.spinner' );

            new DataTable( element, {
                language,
                order: [],
                orderMulti: false,
                fixedColumns: { start: 1, end: 1 },
                scrollX: true,
                initComplete() {
                    $wrapper.removeClass( 'max-h-[75vh] min-h-[25vh] overflow-y-hidden' );
                },
                ajax: async function ( data, callback, settings ) {
                    data.model = element.dataset.model;
                    data.foreign_key = element.dataset.foreignKey;
                    data.foreign_id = element.dataset.foreignId;

                    const url = element.dataset.ajaxUrl;
                    const { data: result } = await axios.post( url, data );
                    callback( result );
                },
                serverSide: true,
            } );

            $( element )
                .on( 'preXhr.dt', function () {
                    $spinner.removeClass( 'hidden' );
                } )
                .on( 'xhr.dt', function () {
                    $spinner.addClass( 'hidden' );
                } );
        } );

    } )();
}
