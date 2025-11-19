<div class="p-4 rounded max-w-lg xl:max-w-xl border" x-cloak x-show="ready">
    <select id="ticket_id" name="ticket_id" x-ref="ticket_id" class="form-select mb-4" @change="refreshPreview()">
        <option value="" hidden>Elige un ticket</option>
        @foreach($tickets as $index => $ticket)
            <option value="{{ $ticket->id }}" @selected($ticket->id == old('ticket_id', $tickets[0]->id))>
                {{ $ticket->name }}
            </option>
        @endforeach
    </select>

    @php $workshop_code = $entity->appointment?->workshop_code @endphp
    <select id="workshop_code" name="workshop_code" x-ref="workshop_code" class="form-select mb-4" @change="refreshPreview()">
        <option value="" hidden>Elige un taller</option>
        @foreach($workshops as $index => $workshop)
            <option value="{{ $workshop->code }}" @selected($workshop->code == old('workshop_code', $workshop_code ?? $workshops[0]->code))>
                {{ sprintf('%s (%s)', $workshop->name, $workshop->code) }}
            </option>
        @endforeach
    </select>

    <div id="preview-toolbar" class="toolbar flex justify-end mb-2">
        <x-button
            type="button" icon="printer" variant="purple" :outline="true" aria-label="Imprimir ticket"
            x-bind:disabled="refreshingPreview"
            x-on:click="$hrefs.iframe.contentWindow.document.print()"
        ></x-button>
    </div>
    <div class="h-full bg-gray-50 rounded border-double border-4 shadow-md" id="preview">
        <iframe scrolling="no" class="w-full" x-ref="iframe"></iframe>
    </div>
</div>
