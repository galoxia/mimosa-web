<script>
    document.addEventListener( 'alpine:init', () => {
        Alpine.data( 'teacherForm', () => {
            const previewUrl = @json(route('api.messages.preview'));
            const showDegreeUrl = @json(route('api.degrees.show', ['degree' => ':degree']));

            return {
                ready: false,
                teacher: @json($entity),
                degree: null, // Titulación actualmente seleccionada
                refreshingPreview: true,

                refreshPreviewDelayed: null,

                init() {
                    this.refreshPreviewDelayed = utils.debounce( this.refreshPreview, 1000 );
                    this.refreshPreviewDelayed();
                    this.ready = true;
                },

                async refreshPreview() {
                    const { $refs: { observations, teacher_number, ticket_id, iframe, name, surname1, surname2 } } = this;
                    const { degrees } = this.teacher;
                    const degree_names = degrees.map( d => d.name );
                    const { name: current_degree_name, abbreviation: current_degree_abbreviation } = this.degree || {};

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
                                degrees: [ ...degree_names, current_degree_name ].filter( d => d ).join( '<br>' ),
                                abbreviation: current_degree_abbreviation
                            }
                        } );
                        iframe.srcdoc = response.data.html;
                    } catch ( err ) {
                        console.error( 'Error al generar la preview: ', err );
                    }
                    this.refreshingPreview = false;
                },

                async loadDegree() {
                    const { $refs: { degree_id, degree_id_footer } } = this;

                    if ( degree_id.value ) {
                        let url = showDegreeUrl.replace( ':degree', degree_id.value );
                        const response = await axios.get( url );
                        const { degree } = response.data;

                        this.degree = degree;

                        const { first_available_teacher_number, next_teacher_number, min_teacher_number, max_teacher_number } = this.degree;
                        degree_id_footer.innerHTML = `Mínimo: ${ min_teacher_number }, Máximo: ${ max_teacher_number }, Siguiente: ${ next_teacher_number }, Primero: ${ first_available_teacher_number }`;
                    } else {
                        this.degree = null;
                    }

                    // this.refreshPreviewDelayed();
                },

                setTeacherNumber() {
                    const { $refs: { teacher_number } } = this;

                    if ( this.degree ) {
                        const { first_available_teacher_number, next_teacher_number, max_teacher_number } = this.degree;

                        if ( next_teacher_number > max_teacher_number ) {
                            teacher_number.value = first_available_teacher_number;
                        } else {
                            teacher_number.value = next_teacher_number;
                        }
                    } else {
                        teacher_number.value = null;
                    }
                },

                async onChangeDegree() {
                    await this.loadDegree();
                    this.setTeacherNumber();
                    this.refreshPreviewDelayed();
                },
            };
        } );
    } );
</script>
