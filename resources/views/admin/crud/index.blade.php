@extends('layouts.dashboard')

@section('title', 'Administración - Lista de ' . $singularName)

@section('main-title')
    <h1 class="text-3xl font-bold mb-8">Lista de {{ $pluralName }}</h1>
@endsection

@section('main-content')
    <div class="flex justify-between items-end mb-4">
        <div class="flex gap-4">
            <x-massive-assignment :model="$model"/>
            @yield('index-left-actions')
        </div>

        <div class="flex gap-4">
            <x-filters :model="$model"/>
            @yield('index-right-actions')
            @if($creatable)
                <x-button-link
                    :href="route('admin.crud.get', ['action' => 'create', 'model' => $model])"
                    icon="plus"
                    variant="success"
                >
                    Crear {{ $singularName }}
                </x-button-link>
            @endif
        </div>
    </div>

    @if($table['rows'] ?? null)
        <x-crud-table :table="$table"/>
    @else
        <x-alert>No se encontraron {{ $pluralName }} en la base de datos.</x-alert>
    @endif
@endsection
