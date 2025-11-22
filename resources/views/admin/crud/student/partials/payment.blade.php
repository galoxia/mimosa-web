<div id="payment" x-effect="pending = Math.round((price - paid - total) * 100) / 100; refreshPreviewDelayed()" class="col-span-1 my-6" x-cloak x-show="ready">
    <div class="flex justify-between items-center form-input py-2 px-2 rounded mb-4">
        <span class="text-lg font-bold">Precio</span>
        <span class="text-2xl"><span x-text="price"></span> €</span>
    </div>

    <div id="total-wrapper" class="mb-4 relative text-info">
        <x-crud-field field="total" :config="$fields['total']"/>
    </div>

    <div class="flex justify-between items-center form-input py-2 px-2 rounded mb-4 text-danger">
        <span class="text-lg font-bold">Pendiente</span>
        <span class="text-2xl"><span x-text="pending"></span> €</span>
    </div>

    <div class="flex justify-end items-center gap-4">
        <x-button type="button" variant="info" @click="total = Math.round((price - paid) / 2 * 100) / 100">Mitad</x-button>
        <x-button type="button" variant="info" @click="total = Math.round((price - paid) * 100) / 100">Todo</x-button>
        <x-button type="button" variant="info" @click="total = 0">Total a 0</x-button>
    </div>
</div>
