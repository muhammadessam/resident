<x-filament-panels::page>
    <x-filament::section>
        {{$this->visitInfoList}}
    </x-filament::section>
    <x-filament::section>
        {{$this->getTable()}}
    </x-filament::section>
    <div class="w-full">
        <div class="mr-auto w-1/2">
            <x-filament::section>
                {{$this->countInfolist}}
            </x-filament::section>
        </div>
    </div>
</x-filament-panels::page>
