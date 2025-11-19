<div class="p-4 rounded max-w-lg xl:max-w-xl border" x-cloak x-show="ready">
    <select id="ticket_id" name="ticket_id" x-ref="ticket_id" class="form-select mb-4" @change="refreshPreview()">
        <option value="" hidden>Elige un ticket</option>
        @foreach($tickets as $index => $ticket)
            <option value="{{ $ticket->id }}" @selected($ticket->id == old('ticket_id', $tickets[0]->id))>
                {{ $ticket->name }}
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
