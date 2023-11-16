<?php

namespace App\Filament\Resources\FemaleResidentResource\Pages;

use App\Filament\Resources\FemaleResidentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFemaleResident extends EditRecord
{
    protected static string $resource = FemaleResidentResource::class;

    protected function getRedirectUrl(): ?string
    {        return $this->getResource()::getUrl('index');

    }
    protected function getActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
