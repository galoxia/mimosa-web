@extends('layouts.dashboard')

@section('main-title')
    <h1 class="text-3xl font-bold">Testing</h1>
@endsection

@section('main-content')
    @php $table = \App\Models\Student::getIndexTable( empty: true ) @endphp

    <x-crud-table :table="$table" :ajax="true"/>
@endsection


