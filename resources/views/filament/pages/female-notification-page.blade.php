<x-filament-panels::page>
    <div class="w-1/4 text-center mx-auto rounded p-5 font-bold text-2xl bg-white shadow-lg" wire:poll.15s wire:poll.15s>
        {{now()->format('H:i  Y-m-d')}}
    </div>

    <livewire:male-resident-in-dept-widget type="female"/>
    <livewire:external-visits-resident-widget type="female"/>
    <livewire:is-out-for-hospital-widget type="female"/>

    <style>
        .fi-sidebar {
            display: none !important;
        }

        .fi-topbar {
            display: none !important;
        }
    </style>
</x-filament-panels::page>
