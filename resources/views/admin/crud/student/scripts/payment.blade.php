<script>
    document.addEventListener( 'alpine:init', () => {
        Alpine.data( 'payment', () => {
            const previewUrl = @json(route( 'api.messages.preview' ));
            const showProductUrl = @json(route('api.products.show', ['product' => ':product', 'degree' => ':degree']));
            const showDegreeUrl = @json(route('api.degrees.show', ['degree' => ':degree']));
            let print_ticket = @json(session('print_ticket', false));

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
                degree_id: @json(old('degree_id', $entity->degree_id)),
                degree: null, // Titulación actualmente seleccionada
                refreshingPreview: true,

                refreshPreviewDelayed: null,

                async init() {
                    this.refreshPreviewDelayed = utils.debounce( this.refreshPreview, 1000 );
                    await this.onChangeDegree();
                    this.ready = true;
                },

                async refreshPreview() {
                    const { $refs } = this;
                    const { wants_group_photos, wants_photo_files, single_marketing_consent, product_id, student_number, degree_id, identification_number, workshop_code, ticket_id, iframe, name, surname1, surname2 } = $refs;
                    const year = getYear();

                    this.refreshingPreview = true;
                    try {
                        const response = await axios.post( previewUrl, {
                            id: ticket_id.value,
                            placeholders: {
                                promotion_year_from: year,
                                promotion_year_to: year + 1,
                                promotion_year_to_yy: ( year + 1 ) % 100,
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
                                paid: -( -this.total ) + this.paid, // Al hacer -this.total, convertimos el string a float.
                                pending: this.pending,
                                today: new Date().toLocaleDateString( 'es-ES' ),
                                single_marketing_consent: single_marketing_consent.options[ single_marketing_consent.selectedIndex ].text.toUpperCase(),
                                wants_photo_files: wants_photo_files.options[ wants_photo_files.selectedIndex ].text.toUpperCase(),
                                wants_group_photos: wants_group_photos.options[ wants_group_photos.selectedIndex ].text.toUpperCase(),
                            }
                        } );
                        iframe.srcdoc = response.data.html;
                    } catch ( err ) {
                        console.error( 'Error al generar la preview: ', err );
                    }
                    this.refreshingPreview = false;

                    if ( print_ticket ) {
                        $refs.iframe.addEventListener( 'load', () => $refs.iframe.contentWindow.print(), { once: true } );
                        print_ticket = false;
                    }
                },

                async onChangeProduct() {
                    await this.loadProduct();
                    this.refreshPreviewDelayed();
                },

                async loadProduct() {
                    const { $refs: { degree_id } } = this;

                    if ( this.product_id ) {
                        let url = showProductUrl
                            .replace( ':product', this.product_id )
                            .replace( ':degree', degree_id.value )
                            .replace( /\/$/, '' );

                        const response = await axios.get( url );
                        const { product: { price, concepts } } = response.data;

                        this.price = Math.round( price * 100 ) / 100; // price es un string, lo convertimos a float de 2 decimales.
                        this.concepts = concepts;
                    } else {
                        this.price = 0;
                        this.concepts = '';
                    }
                },

                async loadDegree() {
                    const { $refs: { degree_id } } = this;

                    if ( degree_id.value ) {
                        let url = showDegreeUrl.replace( ':degree', degree_id.value );
                        const response = await axios.get( url );
                        const { degree } = response.data;

                        this.degree = degree;
                    } else {
                        this.degree = null;
                    }
                },

                setStudentNumber() {
                    const { $refs: { degree_id, student_number } } = this;

                    if ( this.degree ) {
                        // Si la titulación elegida es la que ya tenía el alumno, dejamos el número de estudiante que ya tenía.
                        if ( parseInt( degree_id.value ) === @json($entity->degree_id)) {
                            student_number.value = @json($entity->student_number);
                        } else {
                            const { next_student_number } = this.degree;
                            student_number.value = next_student_number;
                        }
                    } else {
                        student_number.value = null;
                    }
                },

                async onChangeDegree() {
                    await this.loadDegree();
                    await this.loadProduct();
                    this.setStudentNumber();
                    this.refreshPreviewDelayed();
                },
            };
        } );
    } );
</script>
