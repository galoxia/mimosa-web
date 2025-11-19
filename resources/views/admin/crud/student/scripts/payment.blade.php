<script>
    document.addEventListener( 'alpine:init', () => {
        Alpine.data( 'payment', () => {
            const showProductUrl = @json(route('api.products.show', ['product' => ':product', 'degree' => ':degree']));
            const previewUrl = @json(route( 'api.messages.preview' ));

            const getYear = () => {
                const today = new Date();
                const year = today.getFullYear();
                const month = today.getMonth();

                return month >= 8 ? year : year - 1;
            };

            return {
                ready: false,
                price: 0,
                total: @json(old('total', 0)),
                pending: 0,
                paid: @json($entity->paid),
                product_id: @json(old('product_id', $entity->product_id)),
                concepts: '',
                refreshingPreview: true,

                refreshPreviewDelayed: null,

                async init() {
                    this.refreshPreviewDelayed = utils.debounce( this.refreshPreview, 1000 );
                    this.product_id && await this.onChangeProduct();
                    this.ready = true;
                },

                async refreshPreview() {
                    const { $refs } = this;
                    const { single_marketing_consent, product_id, student_number, degree_id, identification_number, workshop_code, ticket_id, iframe, name, surname1, surname2 } = $refs;
                    this.refreshingPreview = true;

                    try {
                        const response = await axios.post( previewUrl, {
                            id: ticket_id.value,
                            placeholders: {
                                year: getYear(),
                                workshop_code: workshop_code.value,
                                name: name.value,
                                surname1: surname1.value,
                                surname2: surname2.value || '&nbsp;',
                                identification_number: identification_number.value,
                                degree: degree_id.selectedIndex ? degree_id.options[ degree_id.selectedIndex ].text : null,
                                student_number: student_number.value,
                                product: product_id.selectedIndex ? product_id.options[ product_id.selectedIndex ].text : null,
                                concepts: ( this.concepts || '' ).replace( /\r\n|\n\r|\r|\n/g, '<br>' ),
                                price: this.price,
                                paid: this.paid + 0 - this.total, // Al hacer 0 - total, convertimos el string a float.
                                pending: this.pending,
                                today: new Date().toLocaleDateString( 'es-ES' ),
                                single_marketing_consent: single_marketing_consent.options[ single_marketing_consent.selectedIndex ].text.toUpperCase(),
                            }
                        } );
                        iframe.srcdoc = response.data.html;
                    } catch ( err ) {
                        console.error( 'Error al generar la preview: ', err );
                    }
                    this.refreshingPreview = false;
                },

                async onChangeProduct() {
                    await this.showProduct();
                },

                async showProduct() {
                    const { $refs: { degree_id } } = this;

                    let url = showProductUrl
                        .replace( ':product', this.product_id )
                        .replace( ':degree', degree_id.value )
                        .replace( /\/$/, '' );

                    const response = await axios.get( url );
                    const { product: { price, concepts } } = response.data;

                    this.price = Math.round( price * 100 ) / 100; // price es un string, lo convertimos a float de 2 decimales.
                    this.concepts = concepts;
                },

                async onChangeDegree() {
                    this.product_id ? await this.showProduct() : this.refreshPreviewDelayed();
                },

                printTicket() {
                    const { $refs: { iframe } } = this;
                    const doc = iframe.contentDocument || iframe.contentWindow.document;
                    // Inyectar CSS de impresión
                    // const style = doc.createElement( 'style' );
                    // style.textContent = `
                    //     @page { margin: 0; }
                    //     @media print {
                    //         td:has(> .content) {
                    //             text-align: left;
                    //         }
                    //     }
                    // `;
                    // doc.head.appendChild( style );

                    iframe.contentWindow.print();
                }
            };
        } );
    } );
</script>
