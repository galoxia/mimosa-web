export default function initEasyMDE() {
    const $inputs = $( 'textarea.js-editor' );
    if ( !$inputs.length ) return;

    ( async () => {
        const { default: EasyMDE } = await import('easymde');

        window.EasyMDE = new WeakMap();

        $inputs.each( function ( _, element ) {
            const easyMDE = new EasyMDE( {
                element,
                toolbar: [ 'bold', 'italic', 'heading', '|', 'quote', 'unordered-list', 'ordered-list', '|', 'link', 'image', 'table', 'horizontal-rule', '|', 'preview', 'guide' ],
                spellChecker: false,
                maxHeight: '300px',
            } );

            window.EasyMDE.set( element, easyMDE );

            easyMDE.codemirror.on( 'change', () => {
                document.dispatchEvent( new CustomEvent( 'EasyMDE:change', { detail: { easyMDE } } ) );
            } );
        } );

        document.dispatchEvent( new CustomEvent( 'EasyMDE:ready' ) );
        console.log( '✅ EasyMDE cargado correctamente' );
    } )();
}
