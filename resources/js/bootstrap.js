import axios from 'axios';

window.axios = axios;

window.axios.defaults.headers.common[ 'X-Requested-With' ] = 'XMLHttpRequest';

import $ from 'jquery';

window.jQuery = window.$ = $;

// Utilidades de la aplicación
window.utils = window.utils || {};
window.utils.debounce = function debounce( fn, delay ) {
    let t;
    return function ( ...args ) {
        clearTimeout( t );
        t = setTimeout( () => fn.apply( this, args ), delay );
    };
};
window.utils.throttle = function throttle( fn, limit ) {
    let lastCall = 0;
    return function ( ...args ) {
        const now = Date.now();
        if ( now - lastCall >= limit ) {
            lastCall = now;
            fn.apply( this, args );
        }
    };
};

// window.onLivewireReady = function ( callback ) {
//     if ( window[ 'Livewire' ] ) {
//         // Livewire ya existe, dispara callback al toque
//         callback( window[ 'Livewire' ] );
//     } else {
//         // Livewire aún no ha inicializado, engánchate al evento
//         document.addEventListener( 'livewire:init', () => {
//             callback( window[ 'Livewire' ] );
//         }, { once: true } );
//     }
// };
