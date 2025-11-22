@extends('layouts.dashboard')

@section('main-title')
    <h1 class="text-3xl font-bold">Mi cita</h1>
@endsection

@section('main-content')
    <livewire:show-my-appointment/>

{{--    @if(count($options) > 1)--}}
{{--        <div class="p-4 border mb-6 dark:border-white/30">--}}
{{--            <p class="mb-3 text-lg">--}}
{{--                Elige el lugar donde hacerte la foto para acceder a su calendario de citas. Si la hora que quieres ya está reservada, prueba en otro lugar:--}}
{{--            </p>--}}
{{--            <div class="flex justify-start items-stretch gap-4">--}}
{{--                @foreach($options as $id => $name)--}}
{{--                    <x-button-link--}}
{{--                        href="{{ route('account.dashboard', [ 'workshop_id' => $id ]) }}"--}}
{{--                        outline--}}
{{--                        variant="purple"--}}
{{--                        icon="{{ $id === $calendar->workshop_id ? 'check' : null }}"--}}
{{--                    >--}}
{{--                        {{ $name }}--}}
{{--                    </x-button-link>--}}
{{--                @endforeach--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    @endif--}}

    @if($calendars)
        <x-calendar-view :calendars="$calendars"/>
{{--        <livewire:calendar-view :calendars="$calendars"/>--}}
    @else
        <x-alert size="xl">Aún no hay calendarios abiertos este año.</x-alert>
    @endif
@endsection

