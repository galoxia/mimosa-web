@extends('layouts.app')

@section('title', 'Registro')

@section('main')
    <div class="flex flex-col items-center justify-center gap-8 p-4">
        <div class="w-full max-w-lg mt-20 bg-white dark:bg-gray-800 shadow-md rounded px-8 py-6">
            <h2 class="text-2xl font-bold text-center text-gray-800 dark:text-white mb-6">Registro de Clientes</h2>

            <x-form-error-list :errors="$errors"/>

            <p class="mb-4 text-sm">Rellena el formulario con tus datos para darte de alta y poder reservar hora para tus fotos.</p>

            <p class="mb-8 text-sm"><strong>IMPORTANTE: Tal y como escribas tu nombre y apellidos, así aparecerán en la orla. Si tienes nombres o apellidos compuestos procura abreviarlos. Escribe tanto en el nombre como los apellidos la primera letra en mayúsculas y el resto en minúsculas.</strong></p>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="mb-4">
                    <input id="name" type="text" name="name" value="{{ old('name') }}" autocomplete="name" autofocus placeholder="Nombre *" class="form-control form-input" required>
                </div>

                <div class="mb-4">
                    <div class="flex items-center justify-between gap-4">
                        <input id="surname1" type="text" name="surname1" value="{{ old('surname1') }}" autocomplete="name" required placeholder="Primer apellido *" class="form-control form-input">
                        <input id="surname2" type="text" name="surname2" value="{{ old('surname2') }}" autocomplete="name" placeholder="Segundo apellido" class="form-control form-input">
                    </div>
                </div>

                <div class="mb-4">
                    <select id="institution_id" name="institution_id" class="form-control form-select" required>
                        <option value="" hidden>Centro de estudios *</option>
                    </select>
                </div>

                <div class="mb-4">
                    <select id="degree_id" name="degree_id" class="form-control form-select" required>
                        <option value="" hidden>Titulación *</option>
                    </select>
                </div>

                <div class="mb-4">
                    <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" placeholder="Email *" required class="form-control form-input">
                </div>

                <div class="mb-4">
                    <input id="identification_number" type="text" name="identification_number" autocomplete="off" value="{{ old('identification_number') }}" placeholder="DNI/NIF *" required class="form-control form-input">
                    <p class="validation-error mt-2"></p>
                </div>

                <div class="mb-4">
                    <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel" placeholder="Teléfono/Móvil *" required class="form-input">
                </div>

                <div class="mb-4">
                    <div class="flex items-center justify-between gap-4">
                        <x-password-input autocomplete="new-password" placeholder="Contraseña *"/>
                        <x-password-input autocomplete="new-password" name="password_confirmation" placeholder="Repetir contraseña *"/>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="flex items-center justify-between gap-4">
                        <select name="single_marketing_consent" id="single_marketing_consent" required class="form-control form-select w-auto">
                            <option value="" hidden>Elige *</option>
                            <option value="1" @selected(old('single_marketing_consent') === '1')>Sí</option>
                            <option value="0" @selected(old('single_marketing_consent') === '0')>No</option>
                        </select>
                        <p class="text-sm">
                            Podéis utilizar mis fotos individuales en la página web, redes sociales, anuncios y tienda de Foto Mimosa
                        </p>
                    </div>
                </div>

                <div class="mb-6">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <input type="checkbox" value="1" name="group_marketing_consent" id="group_marketing_consent" required class="w-5 h-5" {{ old('group_marketing_consent') ? 'checked' : '' }}>
                        </div>
                        <p class="text-sm">
                            (*) Autorizo a cualquier tercero a publicar mis orlas, graduaciones y fotos grupales, en la web, redes sociales y en la tienda de Foto Mimosa
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-center">
                    <button type="submit" class="btn btn-primary w-full">
                        Registrarme
                    </button>
                </div>

                <div class="text-center mt-4">
                    <p class="text-sm">
                        ¿Ya estás registrado?
                        <x-link :href="route('login')">Iniciar sesión</x-link>
                    </p>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    @include('partials.scripts.institution-degree-selectors', [
        'institutions' => $institutions,
        'degrees' => $degrees,
        'institution_id' => old('institution_id'),
        'degree_id' => old('degree_id')
    ])
    @include('partials.scripts.dni-nie-validation', ['input_id' => 'identification_number'])
@endsection
