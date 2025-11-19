<script>
    document.addEventListener( 'EasyMDE:ready', () => {
        const bodyEasyMDE = window.EasyMDE.get( document.getElementById( 'body' ) );
        const previewUrl = @json(route( 'api.messages.preview' ));
        const $iframe = $( '#preview iframe' );
        const $showBackground = $( '#show_background' );

        const refreshPreview = async ( easyMDE ) => {
            try {
                const response = await axios.post( previewUrl, {
                    content: easyMDE.value(),
                    showBackground: $showBackground.val(),
                } );
                $iframe[ 0 ].srcdoc = response.data.html;
            } catch ( err ) {
                console.error( 'Error al generar la preview: ', err );
            }
        };
        const refreshPreviewDelayed = utils.debounce( refreshPreview, 1000 );

        document.addEventListener( 'EasyMDE:change', ( e ) => {
            refreshPreviewDelayed( e.detail.easyMDE );
        } );

        $showBackground.on( 'change', () => {
            refreshPreview( bodyEasyMDE );
        } );

        refreshPreview( bodyEasyMDE );
    } );
</script>
