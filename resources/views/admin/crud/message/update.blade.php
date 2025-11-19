@extends('admin.crud.update')

@section('main-title')
    @parent

    <h2 class="text-muted mt-1">
        <span>Tipo: <strong>{{ \App\Models\Message::TYPES[ $entity->type ]['label'] }}</strong></span>
    </h2>
@endsection

@section('begin-crud-section-1')
    <div class="col-span-1">
        <div class="relative p-4 pt-12 rounded max-w-lg xl:max-w-xl border">
            <span class="text-xl font-bold mb-4 absolute top-4 left-4">Preview</span>
            <div class="h-full bg-gray-50 rounded border-double border-4 shadow-md" id="preview">
                <iframe scrolling="no" class="w-full"></iframe>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @include('admin.crud.message.scripts.set-placeholders-label')
    @include('admin.crud.message.scripts.show-preview')
@endsection
