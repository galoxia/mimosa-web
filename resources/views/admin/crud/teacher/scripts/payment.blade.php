<script>
    document.addEventListener( 'alpine:init', () => {
        Alpine.data( 'payment', () => {
            const previewUrl = @json(route('api.messages.preview'));
            const teacher = @json($entity);
            const { degrees } = teacher;
            const degree_names = degrees.map( d => d.name );

            return {
                ready: false,
                refreshingPreview: true,

                refreshPreviewDelayed: null,

                init() {
                    this.refreshPreviewDelayed = utils.debounce( this.refreshPreview, 1000 );
                    this.ready = true;
                    this.refreshPreview();
                },

                async refreshPreview() {
                    const { $refs } = this;
                    const { degree_id, observations, teacher_number, ticket_id, iframe, name, surname1, surname2 } = $refs;
                    let degree_name = degree_id.selectedIndex ? degree_id.options[ degree_id.selectedIndex ].text : null;

                    this.refreshingPreview = true;
                    try {
                        const response = await axios.post( previewUrl, {
                            id: ticket_id.value,
                            placeholders: {
                                name: name.value,
                                surname1: surname1.value,
                                surname2: surname2.value || '&nbsp;',
                                teacher_number: teacher_number.value,
                                observations: observations.value || '&nbsp;',
                                today: new Date().toLocaleDateString( 'es-ES' ),
                                degrees: [ ...degree_names, degree_name ].filter( d => d ).join( '<br>' ),
                            }
                        } );
                        iframe.srcdoc = response.data.html;
                    } catch ( err ) {
                        console.error( 'Error al generar la preview: ', err );
                    }
                    this.refreshingPreview = false;
                },

                printTicket() {
                    const { $refs: { iframe } } = this;
                    const doc = iframe.contentDocument || iframe.contentWindow.document;
                    // Inyectar CSS de impresión
                    const style = doc.createElement( 'style' );
                    style.textContent = `
                        @page { margin: 0; }
                        @media print {
                            td:has(> .content) {
                                text-align: left;
                            }
                        }
                    `;
                    doc.head.appendChild( style );

                    iframe.contentWindow.print();
                }
            };
        } );
    } );
</script>
