<?php

namespace App\Filament\Resources\FemaleResidentResource\Pages;

use App\Filament\Resources\FemaleResidentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFemaleResidents extends ListRecords
{
    protected static string $resource = FemaleResidentResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
