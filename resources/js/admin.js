import './bootstrap';

import initCalendar from './features/calendar.js';
import initDataTables from './features/datatables.js';
import initSwal from './features/swal.js';
import initEasyMDE from './features/easymde.js';

document.addEventListener( 'DOMContentLoaded', () => {
    initCalendar();
    initDataTables();
    initSwal();
    initEasyMDE();

    // El código para redimensionar los iframes de previews lo podemos poner aquí
    $( '#preview iframe' ).on( 'load', function () {
        try {
            const doc = this.contentDocument || this.contentWindow.document;
            this.style.height = doc.body.scrollHeight + 'px';
        } catch ( err ) {
            console.error( 'No se pudo ajustar la altura del iframe: ', err );
        }
    } );
} );


