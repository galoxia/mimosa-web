@extends('layouts.dashboard')

@section('title', "Administración - Editar $singular_name")

@section('main-title')
    <h1 class="text-3xl font-bold">{{ sprintf("Editar %s - %s", $singular_name, $entity) }}</h1>
@endsection

@section('main-actions')
    @php $model = $entity::class @endphp
    @if($model::isCreatable())
        <x-button-link
            :href="route('admin.crud.get', ['action' => 'create', 'model' => $model])"
            icon="plus"
            variant="success"
        >
            Crear {{ $model::getSingularName() }}
        </x-button-link>
    @endif

    <button type="submit" form="crud-form" name="action" value="update" class="btn btn-info">
        Guardar
    </button>

    <button type="submit" form="crud-form" name="action" value="updateThenUpdate" class="btn btn-info">
        Guardar y editar
    </button>
@endsection

@section('main-content')
    @php $alpine = $fields['_all']['alpine'] ?? null; @endphp
    <form
        method="POST"
        action="{{ route('admin.crud.post', ['action' => 'update']) }}"
        id="crud-form"
        enctype="{{ $fields['_all']['enctype'] ?? 'application/x-www-form-urlencoded' }}"
        @if($alpine) x-data="{{ $alpine }}" @endif
    >
        <input type="hidden" name="id" value="{{ $entity->id }}">

        @include('admin.crud.crud-fields')
    </form>
@endsection

@section('main-content-footer')
    @php
        $foreign_key = $entity->getForeignKey();
        $foreign_id = $entity->id;
        $redirect_url = url()->full();
    @endphp

    @foreach($collections as $config)
        @php $model = $config['model']; @endphp
        <div class="mb-8">
            <div class="flex justify-between items-end mb-4">
                <p class="text-xl font-bold">
                    {{ $config['label'] ?? "Lista de " . $config['pluralName'] }}
                </p>

                <div>
                    @if($config['creatable'])
                        @php $action = 'create'; @endphp
                        <x-button-link
                            :href="route('admin.crud.get', compact('action', 'model', 'foreign_key', 'foreign_id', 'redirect_url'))"
                            icon="plus"
                            variant="success"
                        >
                            Crear {{ $config['singularName'] }}
                        </x-button-link>
                    @endif
                </div>
            </div>

            <x-crud-table :table="$config['table']" :redirect_url="$redirect_url"></x-crud-table>
        </div>
    @endforeach
@endsection
