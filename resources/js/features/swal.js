export default function initSwal() {
    const $forms = $( 'form.js-confirm' );
    if ( !$forms.length ) return;

    ( async () => {
        const { default: Swal } = await import('sweetalert2');
        console.log( '✅ SweetAlert2 cargado correctamente' );

        $forms.on( 'submit', function ( e ) {
            e.preventDefault();

            Swal.fire( {
                title: '¿Estás seguro?',
                text: 'No podrás deshacer esta acción',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí',
                cancelButtonText: 'Cancelar',
            } ).then( result => {
                if ( result.isConfirmed ) e.target.submit();
            } );
        } );
    } )();
}
