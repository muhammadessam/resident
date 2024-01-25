<x-filament-panels::page class="w-full">
    <div class="w-1/4 text-center mx-auto rounded p-5 font-bold text-2xl bg-white shadow-lg" wire:poll.15s wire:poll.15s>
        {{now()->format('H:i  Y-m-d')}}
    </div>






    <style>
        .fi-sidebar {
            display: none !important;
        }

        .fi-topbar {
            display: none !important;
        }
    </style>
</x-filament-panels::page>
