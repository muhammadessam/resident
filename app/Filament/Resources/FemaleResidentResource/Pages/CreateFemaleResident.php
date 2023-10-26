<?php

namespace App\Filament\Resources\FemaleResidentResource\Pages;

use App\Filament\Resources\FemaleResidentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFemaleResident extends CreateRecord
{
    protected static string $resource = FemaleResidentResource::class;

    protected function getActions(): array
    {
        return [

        ];
    }
}
