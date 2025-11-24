export default function initDataTables() {
    const $dataTables = $( '.js-datatable' );
    const $ajaxDataTables = $( '.js-ajax-datatable' );

    if ( !$dataTables.length && !$ajaxDataTables.length ) return;

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

        $dataTables.each( function ( _, element ) {
            new DataTable( element, {
                language,
                // columnDefs: [
                //     { targets: 'js-dt-actions', width: '1%', orderable: false, searchable: false, className: 'actions-col' }
                // ],
                // responsive: true,
                // order: [ [ 0, 'desc' ], [ 1, 'asc' ] ],
                order: [],
                fixedColumns: {
                    start: 1,
                    end: 1,
                },
                scrollX: true,
                initComplete() {
                    const $wrapper = $( element ).closest( '.js-wrapper' );
                    $wrapper.removeClass( 'max-h-[75vh] min-h-[25vh] overflow-y-hidden' );
                    $wrapper.children( '.spinner' ).addClass( 'hidden' );
                },
            } );
        } );

        $ajaxDataTables.each( function ( _, element ) {
            const $wrapper = $( element ).closest( '.js-wrapper' );
            const $spinner = $wrapper.children( '.spinner' );

            new DataTable( element, {
                language,
                // columnDefs: [
                //     { targets: 'js-dt-actions', width: '1%', orderable: false, searchable: false, className: 'actions-col' }
                // ],
                order: [],
                fixedColumns: { start: 1, end: 1 },
                scrollX: true,
                initComplete() {
                    $wrapper.removeClass( 'min-h-[25vh]' );
                },
                ajax: async function ( data, callback, settings ) {
                    data.model = element.dataset.model;
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
