<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    {{-- Etiquetas del head comunes --}}
    @include('partials.head')
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/admin.css', 'resources/js/admin.js'])
    @else
        {{--    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>--}}
    @endif

    <title>@yield('title', 'Dashboard')</title>

    @yield('head')
</head>
<body class="font-sans antialiased bg-gray-100 text-black/70 dark:bg-gray-900 dark:text-white/90">
<div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="bg-gray-200 dark:bg-gray-800 px-4 py-6">
        <header class="mb-6">
            <h1 class="text-2xl mb-4">
                <span class="text-red-600 font-medium">Foto Estudio</span>
                <span class="block text-3xl font-light">MIMOSA</span>
            </h1>
            <h2 class="text-xl font-light opacity-70">
                Área personal
            </h2>
            {{--            @yield('aside-header')--}}
        </header>

        <ul class="space-y-2 font-medium">
            <li>
                <x-dashboard-link :href="route('account.dashboard')" icon="camera">Mi cita</x-dashboard-link>
            </li>
            <li>
                <x-dashboard-link :href="route('account.profile.edit')" icon="user">Mis datos</x-dashboard-link>
            </li>
            @admin
            <li>
                <x-dashboard-link :href="route('admin.dashboard')" icon="chart-bar">Panel</x-dashboard-link>
            </li>
            <li>
                <x-dashboard-link :href="route('admin.crud.get', ['action' => 'index', 'model' => \App\Models\Appointment::class])" icon="bell">
                    {{ \App\Models\Appointment::getPluralName() }}
                </x-dashboard-link>
            </li>
            <li>
                <x-dashboard-link :href="route('admin.crud.get', ['action' => 'index', 'model' => \App\Models\Workshop::class])" icon="wrench">
                    {{ \App\Models\Workshop::getPluralName() }}
                </x-dashboard-link>
            </li>
            <li>
                <x-dashboard-link :href="route('admin.crud.get', ['action' => 'index', 'model' => \App\Models\Calendar::class])" icon="calendar">
                    {{ \App\Models\Calendar::getPluralName() }}
                </x-dashboard-link>
            </li>
            <li>
                <x-dashboard-link :href="route('admin.crud.get', ['action' => 'index', 'model' => \App\Models\Schedule::class])" icon="clock">
                    {{ \App\Models\Schedule::getPluralName() }}
                </x-dashboard-link>
            </li>
            <li>
                <x-dashboard-link :href="route('admin.crud.get', ['action' => 'index', 'model' => \App\Models\Institution::class])" icon="building-library">
                    {{ \App\Models\Institution::getPluralName() }}
                </x-dashboard-link>
            </li>
            <li>
                <x-dashboard-link :href="route('admin.crud.get', ['action' => 'index', 'model' => \App\Models\Degree::class])" icon="academic-cap">
                    {{ \App\Models\Degree::getPluralName() }}
                </x-dashboard-link>
            </li>
            <li>
                <x-dashboard-link :href="route('admin.crud.get', ['action' => 'index', 'model' => \App\Models\Student::class])" icon="user-circle">
                    {{ \App\Models\Student::getPluralName() }}
                </x-dashboard-link>
            </li>
            <li>
                <x-dashboard-link :href="route('admin.crud.get', ['action' => 'index', 'model' => \App\Models\Teacher::class])" icon="beaker">
                    {{ \App\Models\Teacher::getPluralName() }}
                </x-dashboard-link>
            </li>
            <li>
                <x-dashboard-link :href="route('admin.crud.get', ['action' => 'index', 'model' => \App\Models\Product::class])" icon="briefcase">
                    {{ \App\Models\Product::getPluralName() }}
                </x-dashboard-link>
            </li>
            <li>
                <x-dashboard-link :href="route('admin.crud.get', ['action' => 'index', 'model' => \App\Models\Price::class])" icon="currency-euro">
                    {{ \App\Models\Price::getPluralName() }}
                </x-dashboard-link>
            </li>
            <li>
                <x-dashboard-link :href="route('admin.crud.get', ['action' => 'index', 'model' => \App\Models\Message::class])" icon="envelope">
                    {{ \App\Models\Message::getPluralName() }}
                </x-dashboard-link>
            </li>
            <li>
                <x-dashboard-link :href="route('admin.crud.get', ['action' => 'index', 'model' => \App\Models\User::class])" icon="users">
                    {{ \App\Models\User::getPluralName() }}
                </x-dashboard-link>
            </li>
            @endadmin
            <li>
                <x-dashboard-link href="https://www.fotomimosa.es/web/" icon="home">Web MIMOSA</x-dashboard-link>
            </li>
            <li>
                <x-dashboard-link :href="route('account.help')" icon="question-mark-circle">Ayuda</x-dashboard-link>
            </li>
            @yield('aside-links')
        </ul>

        {{--        <footer>--}}
        {{--            @yield('aside-footer')--}}
        {{--        </footer>--}}
    </aside>

    <main class="flex-auto py-6 px-12">
        <header class="mb-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    @yield('main-toolbar-left')
                </div>

                <div class="flex items-center gap-4">
                    @yield('main-toolbar-right')

                    <span>Bienvenid@, {{ auth()->user()->public_name }}</span>
                    <x-logout-icon/>
                </div>
            </div>

            <div class="flex justify-between items-start">
                <div>
                    @yield('main-title')
                </div>

                <div class="flex gap-4">
                    @yield('main-actions')
                </div>
            </div>
        </header>

{{--        @section('main-content')--}}
{{--            <div class="flex flex-col xl:flex-row items-stretch gap-12 mb-12">--}}
{{--                @yield('main-content-left')--}}
{{--                @yield('main-content-right')--}}
{{--            </div>--}}
{{--        @show--}}
        @yield('main-content')

        @yield('main-content-footer')

        <footer class="border-t border-t-gray-400 py-6 mt-16">
            <div class="flex flex-col items-center justify-center text-gray-400 font-semibold">
                <span>FOTO ESTUDIO MIMOSA</span>
                <span>Corrales de Monroy, nº6</span>
                <span>37005 SALAMANCA</span>
                <span>Tel: +34923264329</span>
            </div>

            @yield('main-footer')
            <x-status-bar :variant="session('variant', 'info')">{{ session('status') }}</x-status-bar>
        </footer>
    </main>
</div>

@yield('scripts')
<livewire:dummy/>
</body>
</html>
