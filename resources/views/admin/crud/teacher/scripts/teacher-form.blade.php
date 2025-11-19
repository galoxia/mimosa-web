<script>
    document.addEventListener( 'alpine:init', () => {
        Alpine.data( 'teacherForm', () => {
            const previewUrl = @json(route('api.messages.preview'));
            const showDegreeUrl = @json(route('api.degrees.show', ['degree' => ':degree']));

            return {
                ready: false,
                refreshingPreview: true,
                teacher: @json($entity),
                degree: null, // Titulación actualmente seleccionada

                refreshPreviewDelayed: null,

                init() {
                    this.refreshPreviewDelayed = utils.debounce( this.refreshPreview, 1000 );
                    this.ready = true;
                    this.refreshPreview();
                },

                async refreshPreview() {
                    const { $refs: { observations, teacher_number, ticket_id, iframe, name, surname1, surname2 } } = this;
                    const { degrees } = this.teacher;
                    const degree_names = degrees.map( d => d.name );

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
                                degrees: [ ...degree_names, this.degree?.name ].filter( d => d ).join( '<br>' ),
                            }
                        } );
                        iframe.srcdoc = response.data.html;
                    } catch ( err ) {
                        console.error( 'Error al generar la preview: ', err );
                    }
                    this.refreshingPreview = false;
                },

                async onChangeDegree() {
                    const { $refs: { teacher_number, teacher_number_footer, degree_id, degree_id_footer } } = this;

                    if ( degree_id.value ) {
                        let url = showDegreeUrl.replace( ':degree', degree_id.value );
                        const response = await axios.get( url );
                        const { degree } = response.data;

                        this.degree = degree;
                        const { next_teacher_number, min_teacher_number, max_teacher_number } = degree;

                        degree_id_footer.innerHTML = `Min: ${ min_teacher_number }, Max: ${ max_teacher_number }`;
                        teacher_number.value = next_teacher_number;
                        if ( next_teacher_number > max_teacher_number ) {
                            teacher_number_footer.innerHTML = `El número es superior al máximo de la titulación ${ max_teacher_number }`;
                        }
                    } else {
                        teacher_number.value = null;
                    }

                    await this.refreshPreview();
                },
            };
        } );
    } );
</script>
