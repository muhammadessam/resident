<?php

namespace App\Filament\Resources\MaleResidentResource\Pages;

use App\Filament\Resources\MaleResidentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMaleResident extends CreateRecord
{
    protected static string $resource = MaleResidentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type'] = 'male';
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
