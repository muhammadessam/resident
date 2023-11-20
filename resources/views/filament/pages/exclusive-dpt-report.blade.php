<x-filament-panels::page>
    {{$this->table}}

    <x-filament::section class="w-1/2 mr-auto">
        <x-slot name="heading">
            <h1 class="font-bold">المجموع</h1>
        </x-slot>
        {{$this->countInfolist}}
    </x-filament::section>
</x-filament-panels::page>
