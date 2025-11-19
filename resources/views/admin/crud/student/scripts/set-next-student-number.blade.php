<script>
    document.addEventListener( 'DOMContentLoaded', function () {
        const degreeSelect = /** @type {HTMLSelectElement} */ document.getElementById( 'degree_id' );
        const studentNumberInput = /** @type {HTMLInputElement} */ document.getElementById( 'student_number' );
        const nextStudentNumberUrl = @json(route('api.degrees.next-student-number', ['degree' => ':degree']));
        const student = @json($entity);
        const { student_number, degree_id } = student;

        degreeSelect.addEventListener( 'change', async function () {
            if ( this.value ) {
                // Si la titulación elegida es la que ya tenía el alumno, dejamos el número de estudiante que ya tenía.
                if ( this.value.toString() === ( degree_id || 0 ).toString() ) {
                    studentNumberInput.value = student_number;
                } else {
                    const url = nextStudentNumberUrl.replace( ':degree', this.value );
                    const response = await axios.get( url );
                    studentNumberInput.value = response.data.number;
                }
            } else {
                studentNumberInput.value = null;
            }
            studentNumberInput.dispatchEvent( new Event( 'input' ) );
        } );
    } );
</script>
