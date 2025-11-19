@extends('admin.crud.create')

@section('scripts')
    @include('partials.scripts.institution-degree-selectors', [
        'institutions' => $institutions,
        'degrees' => $degrees,
        'institution_id' => old('institution_id', $entity->institution_id),
        'degree_id' => old('degree_id', $entity->degree_id)
    ])
@endsection

