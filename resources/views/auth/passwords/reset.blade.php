@extends('layouts.app')

@section('title', 'Restablecer Contraseña')

@section('main')
    <div class="flex flex-col items-center justify-center gap-8 p-4">
        <div class="w-full max-w-md mt-20 bg-white dark:bg-gray-800 shadow-md rounded px-8 py-6">
            <h2 class="text-2xl font-bold text-center mb-6">
                Restablecer Contraseña
                <span class="text-base block opacity-60">{{ $email }}</span>
            </h2>

            <x-form-error-list :errors="$errors"/>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <div class="mb-4">
                    <label for="password" class="form-label">Nueva contraseña</label>
                    <x-password-input autocomplete="new-password" autofocus/>
                </div>

                <div class="mb-6">
                    <label for="password-confirmation" class="form-label">Confirmar contraseña</label>
                    <x-password-input autocomplete="new-password" name="password_confirmation"/>
                </div>

                <div class="flex items-center justify-center">
                    <button type="submit" class="btn btn-primary w-full">
                        Restablecer Contraseña
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
