@extends('layouts.dashboard')

@section('main-title')
    <h1 class="text-3xl font-bold">Preguntas frecuentes</h1>
@endsection

@section('main-content')
    <ul class="list-none mb-8">
        <li>
            <h3 class="text-xl font-semibold mt-5 mb-3">
                ¿Cómo puedo concertar mi cita?
            </h3>
            <div class="text-muted space-y-2">
                <p>
                    Para concertar cita pulsa
                    <x-link :href="route('account.dashboard')">aquí</x-link>
                    y reserva día y hora entre los días abiertos disponibles (en verde).
                </p>
                <p>
                    Un vez reservada, en la parte superior podrás ver un resumen de tu cita además de algunos consejos importantes para ese día.
                </p>
                <p class="text-danger">
                    <strong>¡IMPORTANTE!</strong>, no se permite cambiar la cita si está programada para las próximas 48 horas (hoy o mañana). Si lo necesitas, por favor contacta con MIMOSA.
                </p>
            </div>
        </li>
        <li>
            <h3 class="text-xl font-semibold mt-5 mb-3">
                ¿Cómo puedo cambiar mis datos personales?
            </h3>
            <div class="text-muted">
                <p>
                    En la sección
                    <x-link :href="route('account.profile.edit')">"Mis datos"</x-link>
                    puedes modificar tus datos personales.
                </p>
            </div>
        </li>
        <li>
            <h3 class="text-xl font-semibold mt-5 mb-3">
                ¿Cómo puedo cambiar mi titulación?
            </h3>
            <div class="text-muted">
                <p>
                    En la sección
                    <x-link :href="route('account.profile.edit')">"Mis datos"</x-link>
                    puedes cambiar la titulación.
                </p>
            </div>
        </li>
        <li>
            <h3 class="text-xl font-semibold mt-5 mb-3">
                ¿Más dudas?
            </h3>
            <div class="text-muted">
                <p>
                    Cualquier otro problema para el que no encuentre solución, por favor notifíquelo a la dirección:
                    <x-link href="mailto:info@fotomimosa.es">info@fotomimosa.es</x-link>
                </p>
            </div>
        </li>
    </ul>
@endsection
