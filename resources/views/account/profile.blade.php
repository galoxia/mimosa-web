@extends('layouts.dashboard')

@section('title', 'Mis datos')

@section('main-title')
    <h1 class="text-3xl font-bold">Mis datos</h1>
@endsection

@section('main-content')
    <div class="max-w-lg">
        @student

        @php $student = auth()->user()->student; @endphp

        <div class="mb-8">
            <p class="font-semibold text-red-700 dark:text-red-500">
                ¡ATENCIÓN! Si cambias de titulación, perderás tu cita y tendrás que solicitar una nueva.
            </p>
        </div>

        <form method="POST" action="{{ route('account.profile.update') }}" class="mb-12">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <input id="name" type="text" name="name" value="{{ old('name', $student->name) }}" autocomplete="name" placeholder="Nombre *" class="form-control form-input" required>
                @error('name')
                <p class="validation-error mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <input id="surname1" type="text" name="surname1" value="{{ old('surname1', $student->surname1) }}" autocomplete="name" required placeholder="Primer apellido *" class="form-control form-input">
                        @error('surname1')
                        <p class="validation-error mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex-1">
                        <input id="surname2" type="text" name="surname2" value="{{ old('surname2', $student->surname2) }}" autocomplete="name" placeholder="Segundo apellido" class="form-control form-input">
                        @error('surname2')
                        <p class="validation-error mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>

            <div class="mb-4">
                <select id="institution_id" name="institution_id" class="form-control form-select" required>
                    <option value="" hidden>Centro de estudios *</option>
                </select>
                @error('institution_id')
                <p class="validation-error mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <select id="degree_id" name="degree_id" class="form-control form-select" required>
                    <option value="" hidden>Titulación *</option>
                </select>
                @error('degree_id')
                <p class="validation-error mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <input disabled id="email" type="email" name="email" value="{{ old('email', auth()->user()->email) }}" autocomplete="email" placeholder="Email *" class="form-control form-input">
            </div>

            <div class="mb-4">
                <input
                    id="identification_number"
                    type="text"
                    name="identification_number"
                    autocomplete="off"
                    value="{{ old('identification_number', $student->identification_number) }}"
                    placeholder="DNI/NIF *"
                    required
                    class="form-control form-input"
                >
                <p class="validation-error mt-2"></p>
                @error('identification_number')
                <p class="validation-error mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <input
                    id="phone" type="tel" name="phone" value="{{ old('phone', $student->phone) }}" autocomplete="tel" placeholder="Teléfono/Móvil *" required
                    class="form-control form-input"
                >
                @error('phone')
                <p class="validation-error mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <div class="flex items-center justify-between gap-4">
                    <select
                        name="single_marketing_consent"
                        id="single_marketing_consent"
                        required
                        class="form-control form-select w-auto"
                    >
                        <option value="" hidden>Elige *</option>
                        <option value="1" @selected((int) old('single_marketing_consent', $student->single_marketing_consent) === 1)>Sí</option>
                        <option value="0" @selected((int) old('single_marketing_consent', $student->single_marketing_consent) === 0)>No</option>
                    </select>
                    <p class="text-sm">
                        Podéis utilizar mis fotos individuales en la página web, redes sociales, anuncios y tienda de Foto Mimosa
                    </p>
                </div>
                @error('single_marketing_consent')
                <p class="validation-error mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-center">
                <button type="submit" class="btn btn-primary w-full">
                    Actualizar datos
                </button>
            </div>
        </form>
        @endstudent

        <div class="mb-8">
            <h1 class="text-2xl font-bold mb-2">
                Cambio de contraseña
            </h1>
            <p class="text-sm">
                Si no recuerdas tu contraseña actual, cierra la sesión y solicita el cambio desde el formulario de "Restablecer contraseña".
            </p>
        </div>

        <form method="POST" action="{{ route('account.profile.update-password') }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <input id="current_password" type="password" name="current_password" autocomplete="current-password" placeholder="Contraseña actual *" required class="form-control form-input">
                @error('current_password')
                <p class="validation-error mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <input id="password" type="password" name="password" autocomplete="new-password" placeholder="Nueva contraseña *" required class="form-control form-input">
                @error('password')
                <p class="validation-error mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" placeholder="Repetir nueva contraseña *" required class="form-control form-input">
            </div>

            <div class="flex items-center justify-center">
                <button type="submit" class="btn btn-primary w-full">
                    Actualizar contraseña
                </button>
            </div>
        </form>
    </div>
@endsection

@section('main-footer')
    @if(!session()->has('status') and $errors->any())
        <x-status-bar variant="error">{{ __('No se pudo realizar la operación. Por favor, revisa los errores del formulario.') }}</x-status-bar>
    @endif
@endsection

@section('scripts')
    @student
    @include('partials.scripts.institution-degree-selectors', [
        'institutions' => $institutions,
        'degrees' => $degrees,
        'institution_id' => old('institution_id', $student->institution_id),
        'degree_id' => old('degree_id', $student->degree_id)
    ])
    @include('partials.scripts.dni-nie-validation', ['input_id' => 'identification_number'])
    @endstudent
@endsection
