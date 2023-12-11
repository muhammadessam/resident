<?php

namespace App\Filament\Resources\MaleResidentResource\Pages;

use App\Filament\Resources\MaleResidentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMaleResidents extends ListRecords
{
    protected static string $resource = MaleResidentResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
