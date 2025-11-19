@extends('admin.crud.create')

@section('begin-crud-section-2')
    <div class="col-span-1">
        @include('admin.crud.teacher.partials.form-ticket')
    </div>
@endsection

@section('scripts')
    @include('admin.crud.teacher.scripts.payment')
    @include('partials.scripts.institution-degree-selectors', [
        'institutions' => $institutions,
        'degrees' => $degrees,
        'institution_id' => old('institution_id', $entity->institution_id),
        'degree_id' => old('degree_id', $entity->degree_id)
    ])
    @include('admin.crud.teacher.scripts.set-next-teacher-number')
@endsection

