<script>
    document.addEventListener( 'DOMContentLoaded', function () {
        const messageTypes = @json(\App\Models\Message::TYPES);
        const entity = @json($entity);
        const bodyFooter = document.querySelector( '#body ~ .crud-field-footer' );

        const typeInput = document.getElementById( 'type' );

        const setPlaceholdersLabel = ( type ) => {
            bodyFooter.innerHTML = 'Etiquetas: ' + Object.keys( messageTypes[ type ][ 'placeholders' ] ).join( ' ' );
            // bodyFooter.innerHTML = 'Etiquetas: ' + messageTypes[ type ][ 'placeholders' ].join( ' ' );
        };

        if ( typeInput ) {
            typeInput.addEventListener( 'change', function () {
                setPlaceholdersLabel( this.value );
            } );
        }

        setPlaceholdersLabel( entity[ 'type' ] );
    } );
</script>
