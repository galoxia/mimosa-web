<script>
    // Verificaciones de DNI/NIE
    document.addEventListener( 'DOMContentLoaded', function () {
        const input = /** @type {HTMLInputElement} */ document.getElementById(@json($input_id));
        const errorLabel = input.nextElementSibling;

        function validateSpanishId( value ) {
            if ( value == null ) return false;
            // Normalizar: quitar espacios y guiones, y poner en mayúsculas
            const doc = String( value ).toUpperCase().replace( /[\s-]+/g, '' );
            // Patrones válidos
            const dniRegex = /^\d{8}[A-Z]$/;        // 8 dígitos + letra
            const nieRegex = /^[XYZ]\d{7}[A-Z]$/;   // X/Y/Z + 7 dígitos + letra

            if ( !dniRegex.test( doc ) && !nieRegex.test( doc ) ) {
                return false;
            }
            // Validamos la letra del documento
            const letters = 'TRWAGMYFPDXBNJZSQVHLCKE';

            let numberPart = '';
            let letter = doc.slice( -1 );

            if ( nieRegex.test( doc ) ) {
                // NIE: mapear X/Y/Z -> 0/1/2 y formar los 8 dígitos
                const map = { X: '0', Y: '1', Z: '2' };
                numberPart = map[ doc[ 0 ] ] + doc.slice( 1, 8 ); // 8 dígitos
            } else {
                // DNI
                numberPart = doc.slice( 0, 8 );
            }
            // Seguridad: debe ser numérico tras el mapeo
            if ( !/^\d{8}$/.test( numberPart ) ) {
                return false;
            }

            const expected = letters[ Number( numberPart ) % 23 ];

            return letter === expected;
        }

        function onInput() {
            if ( /*!input.value ||*/ validateSpanishId( input.value ) ) {
                // input.classList.remove( 'form-input-invalid' );
                errorLabel.textContent = '';
            } else {
                // input.classList.add( 'form-input-invalid' );
                errorLabel.textContent = 'El documento introducido no parece válido. Por favor, verifica que sea correcto antes de enviar el formulario.';
            }
        }

        // Usamos debounce para la validación durante la escritura
        input.addEventListener( 'input', utils.debounce( onInput, 300 ) );
        // Validación inicial (sin debounce) al cargar
        onInput();
    } );
</script>
