@extends('admin.crud.update')

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

    <x-button type="submit" form="crud-form" name="action" value="updateThenUpdate" icon="printer" variant="info">
        Guardar y editar
    </x-button>
@endsection

@section('main-title')
    @parent

    @if($entity->appointment)
        <h2 class="text-muted flex items-center mt-1">
            <x-dynamic-component component="heroicon-o-clock" class="w-[1.2em] h-[1.2em] me-2 -mb-[0.1em]"/>
            <span>El alumno tiene cita el <strong>{{ $entity->appointment }}</strong></span>
        </h2>
    @else
        <h2 class="text-muted">El alumno no tiene cita concertada</h2>
    @endif
@endsection

@section('begin-crud-section-2')
    <div class="col-span-1">
        @include('admin.crud.student.partials.form-ticket')
    </div>
@endsection

@section('after-crud-field-product_id')
    @include('admin.crud.student.partials.payment')
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

