<div>
    <div class="mt-4 p-4 appointments">
        <p class="text-lg font-semibold">Horas para el {{ $date }}</p>
        <p class="text-sm font-semibold mb-8 text-muted">en {{ $workshop_name }}</p>

        @if($date)
            <div class="flex gap-4">
                <div class="flex-1 shrink-0 basis-0">
                    <h2 class="opacity-80 mb-1 uppercase">Mañanas</h2>

                    @if(count(array_filter($morning, fn($appointment) => $appointment['isEnabled'])))
                        <h3 class="opacity-80 mb-4 font-bold text-green-500">Libre</h3>
                    @else
                        <h3 class="opacity-80 mb-4 font-bold text-red-500">Completa</h3>
                    @endif
                    <ul class="flex flex-col gap-4">
                        @foreach ($morning as $time => $appointment)
                            @if($appointment['isEnabled'])
                                <x-button icon="clock" variant="info" class="w-full" title="Reservar" wire:click="book('{{ $time }}')">
                                    {{ $time }}
                                </x-button>
                            @elseif($appointment['isOwn'])
                                <x-button icon="x-circle" variant="danger" class="w-full" title="Cancelar reserva" wire:click="unbook('{{ $time }}')">
                                    {{ $time }}
                                </x-button>
                            @else
                                <div class="btn btn-disabled w-full flex items-center gap-2">
                                    <x-dynamic-component component="heroicon-o-x-circle" class="w-5 h-5"/>
                                    {{ $time }}
                                </div>
                            @endif
                        @endforeach
                    </ul>
                </div>

                <div class="flex-1 shrink-0 basis-0">
                    <h2 class="opacity-80 mb-1 uppercase">Tardes</h2>

                    @if(count(array_filter($afternoon, fn($appointment) => $appointment['isEnabled'])))
                        <h3 class="opacity-80 mb-4 font-bold text-green-500">Libre</h3>
                    @else
                        <h3 class="opacity-80 mb-4 font-bold text-red-500">Completa</h3>
                    @endif
                    <ul class="flex flex-col gap-4">
                        @foreach ($afternoon as $time => $appointment)
                            @if($appointment['isEnabled'])
                                <x-button icon="clock" variant="info" class="w-full" title="Reservar" wire:click="book('{{ $time }}')">
                                    {{ $time }}
                                </x-button>
                            @elseif($appointment['isOwn'])
                                <x-button icon="x-circle" variant="danger" class="w-full" title="Cancelar reserva" wire:click="unbook('{{ $time }}')">
                                    {{ $time }}
                                </x-button>
                            @else
                                <div class="btn btn-disabled w-full flex items-center gap-2">
                                    <x-dynamic-component component="heroicon-o-x-circle" class="w-5 h-5"/>
                                    {{ $time }}
                                </div>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>

    <x-spinner wire:loading.grid size="28"/>
</div>
