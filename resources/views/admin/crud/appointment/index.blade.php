@extends('admin.crud.index')

@section('index-right-actions')
    <x-button-link
        :href="route('admin.crud.get', ['action' => 'create', 'model' => \App\Models\Student::class])"
        icon="plus"
        variant="success"
    >
        Crear {{ \App\Models\Student::getSingularName() }}
    </x-button-link>
@endsection
