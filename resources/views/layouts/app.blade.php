<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    {{-- Etiquetas del head comunes --}}
    @include('partials.head')

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        {{--    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>--}}
    @endif

    <title>@yield('title')</title>

    @yield('head')
</head>
<body class="font-sans antialiased bg-gray-100 text-black/70 font-medium dark:bg-gray-900 dark:text-white/90 min-h-screen">
<header class="bg-white dark:bg-gray-800 p-4">
    <div class="container mx-auto flex justify-between items-center">
        <h1>
            <a href="{{ route('home') }}" class="flex items-end gap-4">
                <span class="text-2xl text-red-600 font-medium">Foto Estudio</span>
                <span class="text-3xl font-light">MIMOSA</span>
            </a>
        </h1>
    </div>
</header>

<main>
    @yield('main')
</main>

<footer>
    @yield('footer')

    <x-status-bar :variant="session('variant', 'info')">{{ session('status') }}</x-status-bar>
</footer>

@yield('scripts')
<livewire:dummy/>
</body>
</html>
