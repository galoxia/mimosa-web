<div>
    <div class="relative py-4 mb-4">
        @if($appointment_id)
            <x-alert icon="exclamation-circle" size="xl">
                Tu cita está reservada para el <span class="font-bold text-2xl">{{ $appointment }}</span>
            </x-alert>

            <div class="mt-4 text-muted">
                <p class="font-bold mb-2">Observaciones:</p>
                <ul class="list-disc list-inside">
                    <li>Con el fin de agilizar el proceso y evitar pérdidas de tiempo, por favor, acude a tu cita 10 minutos antes de la hora acordada (excepto primera hora de la mañana y de la tarde).</li>
                    <li>
                        Los alumn@s con el pelo largo <strong>no pueden acudir a la sesión de fotos con el pelo mojado o con gomina sin secar</strong>.
                        La toga es una prenda sumamente delicada, susceptible de mancharse o deteriorarse con gran facilidad.
                    </li>
                    <li>
                        Los alumn@s que antes se hagan la foto en el estudio serán los que aparezcan primero en la orla.
                        {{--                    La <strong>colocación de las fotos</strong> del alumno en la orla se hará en función del <strong>orden de realización en el estudio</strong> de Foto Mimosa.--}}
                        Si hay alumn@s que, debido a su relación, deseen aparecer juntos, deberán <strong>elegir horas consecutivas.</strong>
                        En contadas ocasiones, por motivos de estética, nos podemos ver obligados a cambiar el orden de algún alumno.
                    </li>
                    <li class="text-danger">
                        <strong>¡IMPORTANTE!</strong>.
                        Rogamos a los alumn@s <strong>no maquillarse</strong> para hacerse las fotos de orla, ya que Foto Mimosa edita posteriormente los archivos digitales para lograr un resultado óptimo y el maquillaje dificulta la edición.
                        No obstante, si optan por maquillarse, rogamos que eviten la <strong>zona del cuello</strong>.
                    </li>
                    <li class="text-danger">
                        <strong>¡IMPORTANTE!</strong>.
                        No se permite cambiar la cita si está programada para las próximas 48 horas (hoy o mañana, excluyendo festivos). Si lo necesitas, por favor contacta con MIMOSA.
                    </li>
                </ul>
            </div>
        @else
            <x-alert size="xl">
                Aún no has concertado cita con nosotros. Por favor, elige el día y la hora en el calendario.
            </x-alert>

            <div class="mt-4 text-muted">
                <p class="font-bold mb-2">Observaciones:</p>
                <ul class="list-disc list-inside">
                    <li class="text-danger">
                        <strong>¡IMPORTANTE!</strong>.
                        No se permite cambiar la cita si está programada para las próximas 48 horas (hoy o mañana, excluyendo domingos y festivos). Si lo necesitas, por favor contacta con MIMOSA.
                    </li>
                </ul>
            </div>
        @endif

        <x-spinner wire:loading.grid size="24" fixed class="z-10"/>
    </div>
</div>
