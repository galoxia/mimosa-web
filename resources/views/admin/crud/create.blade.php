@extends('layouts.dashboard')

@section('title', 'Administración - Añadir ' . $singular_name)

@section('main-title')
    <h1 class="text-3xl font-bold">Añadir {{ $singular_name }}</h1>
@endsection

@section('main-actions')
    <button type="submit" form="crud-form" name="action" value="create" class="btn btn-success">
        Crear
    </button>

    <button type="submit" form="crud-form" name="action" value="createThenCreate" class="btn btn-success">
        Crear y añadir otro
    </button>
@endsection

@section('main-content')
    @php $alpine = $fields['_all']['alpine'] ?? null; @endphp
    <form
        method="POST"
        action="{{ route('admin.crud.post', ['action' => 'create']) }}"
        id="crud-form"
        enctype="{{ $fields['_all']['enctype'] ?? 'application/x-www-form-urlencoded' }}"
        @if($alpine) x-data="{{ $alpine }}" @endif
    >
        @include('admin.crud.crud-fields')
    </form>
@endsection
