@extends('admin.crud.create')

@section('begin-crud-section-2')
    <div class="col-span-1">
        @include('admin.crud.student.partials.form-ticket')
    </div>
@endsection

@section('after-crud-field-product_id')
    @include('admin.crud.student.partials.payment')
@endsection

@section('main-actions')
    <button type="submit" form="crud-form" name="action" value="create" class="btn btn-success">
        Crear
    </button>

    <button type="submit" form="crud-form" name="action" value="createThenCreate" class="btn btn-success">
        Crear y añadir otro
    </button>

    <x-button type="submit" form="crud-form" name="action" value="createThenUpdate" icon="printer" variant="success">
        Crear y editar
    </x-button>
@endsection

@section('scripts')
    @include('admin.crud.student.scripts.payment')

    @include('partials.scripts.institution-degree-selectors', [
        'institutions' => $institutions,
        'degrees' => $degrees,
        'institution_id' => old('institution_id', $entity->institution_id),
        'degree_id' => old('degree_id', $entity->degree_id)
    ])
    @include('partials.scripts.dni-nie-validation', ['input_id' => 'identification_number'])
{{--    @include('admin.crud.student.scripts.set-next-student-number')--}}
@endsection

