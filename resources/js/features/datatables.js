export default function initDataTables() {
    const $dataTables = $( '.js-datatable' );
    if ( !$dataTables.length ) return;

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
                columnDefs: [
                    { targets: 'js-dt-actions', width: '1%', orderable: false, responsivePriority: 1 }
                ],
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

    } )();
}
