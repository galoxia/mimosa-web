@extends('layouts.app')

@section('title', 'Restablecer Contraseña')

@section('main')
    <div class="flex flex-col items-center justify-center gap-8 p-4">
        <div class="w-full max-w-md mt-20 bg-white dark:bg-gray-800 shadow-md rounded px-8 py-6">
            <h2 class="text-2xl font-bold text-center mb-6">Restablecer Contraseña</h2>

            <x-form-error-list :errors="$errors"/>

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="mb-6">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus class="form-control form-input">
                    <p class="text-sm mt-1">
                        Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
                    </p>
                </div>

                <div class="flex items-center justify-center">
                    <button type="submit" class="btn btn-primary w-full">
                        Enviar Enlace
                    </button>
                </div>

                <p class="text-center mt-4 text-sm">
                    Volver a
                    <x-link :href="route('login')">Iniciar sesión</x-link>
                </p>
            </form>
        </div>
    </div>
@endsection
