@props(['errors'])

@if ($errors->any())
    <div class="bg-red-100 border border-red-700 text-red-700 px-4 py-3 rounded mb-4 text-sm dark:bg-red-950 dark:text-red-400 dark:border-red-400">
        <p class="mb-2 flex items-center gap-1">
            <x-heroicon-c-exclamation-triangle class="w-5 h-5"/>
            <span>Existen errores en el formulario</span>
        </p>
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
