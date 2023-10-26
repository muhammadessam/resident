<?php

namespace App\Filament\Resources\FemaleResidentResource\Pages;

use App\Filament\Resources\FemaleResidentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFemaleResident extends CreateRecord
{
    protected static string $resource = FemaleResidentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type'] = 'female';
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getActions(): array
    {
        return [

        ];
    }
}
