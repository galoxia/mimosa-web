<script>
    document.addEventListener( 'DOMContentLoaded', function () {
        const degreeSelect = /** @type {HTMLSelectElement} */ document.getElementById( 'degree_id' );
        const institutionSelect = /** @type {HTMLSelectElement} */ document.getElementById( 'institution_id' );

        const institutions = @json($institutions);
        const degrees = @json($degrees);

        function populateDegrees( institutionId, degreeId ) {
            // Quitamos las opciones que tengan un valor (todas menos la opción "placeholder")
            degreeSelect.querySelectorAll( 'option[value]:not([value=""])' ).forEach( o => o.remove() );
            // Agregamos las opciones al select
            ( degrees[ institutionId ] || [] ).map( d => {
                degreeSelect.add( new Option( d.name, d.id, false, d.id === degreeId ) );
            } );
            // degreeSelect.dispatchEvent( new Event( 'change', { bubbles: true } ) );
            degreeSelect.dispatchEvent( new Event( 'change' ) );
        }

        const institutionId = Number(@json($institution_id));
        const degreeId = Number(@json($degree_id));

        institutions.forEach( i => {
            institutionSelect.add( new Option( i.name, i.id, false, institutionId === i.id ) );
        } );

        institutionSelect.addEventListener( 'change', function () {
            populateDegrees( Number( this.value ) );
        } );

        populateDegrees( institutionId, degreeId );
    } );
</script>
