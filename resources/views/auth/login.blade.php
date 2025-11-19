@extends('layouts.app')

@section('title', 'Login')

@section('main')
    <div class="flex flex-col items-center justify-center gap-8 p-4">
        <div class="w-full max-w-md mt-20 bg-white dark:bg-gray-800 shadow-md rounded px-8 py-6">
            <h2 class="text-2xl font-bold text-center mb-6">Iniciar Sesión</h2>

            <x-form-error-list :errors="$errors"/>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-4">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus class="form-control form-input">
                </div>

                <div class="mb-6">
                    <label for="password" class="form-label">Contraseña</label>
                    <x-password-input/>
                </div>

                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-2">
                        <input id="remember" type="checkbox" name="remember" class="w-4 h-4">
                        <label for="remember" class="text-sm">
                            Recordarme
                        </label>
                    </div>
                    <div>
                        <x-link :href="route('password.request')" class="text-sm">¿Olvidaste tu contraseña?</x-link>
                    </div>
                </div>

                <div class="flex items-center justify-center">
                    <button type="submit" class="btn btn-primary w-full">
                        Entrar
                    </button>
                </div>
            </form>

            <p class="text-center mt-4 text-sm">
                ¿Nuevo cliente?
                <x-link :href="route('register')">Regístrate</x-link>
            </p>
        </div>
    </div>
@endsection
