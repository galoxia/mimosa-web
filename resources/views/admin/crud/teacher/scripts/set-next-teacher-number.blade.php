<script>
    document.addEventListener( 'DOMContentLoaded', function () {
        const degreeSelect = /** @type {HTMLSelectElement} */ document.getElementById( 'degree_id' );
        const teacherNumberInput = /** @type {HTMLInputElement} */ document.getElementById( 'teacher_number' );
        const nextTeacherNumberUrl = @json(route('api.degrees.next-teacher-number', ['degree' => ':degree']));
        const teacher = @json($entity);
        const { teacher_number, degrees } = teacher;

        degreeSelect.addEventListener( 'change', async function () {
            // if ( this.value ) {
            //     // Si la titulación elegida es alguna de las que ya tenía el profesor, dejamos el número que ya tenía.
            //     // if ( this.value.toString() === ( degree_id || 0 ).toString() ) {
            //     //     teacherNumberInput.value = teacher_number;
            //     // } else {
            //     //     const url = nextTeacherNumberUrl.replace( ':degree', this.value );
            //     //     const response = await axios.get( url );
            //     //     teacherNumberInput.value = response.data.number;
            //     // }
            // } else {
            //     teacherNumberInput.value = null;
            // }
            teacherNumberInput.dispatchEvent( new Event( 'input' ) );
        } );
    } );
</script>
