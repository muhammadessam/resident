<?php

namespace App\Filament\Resources\MaleResidentResource\Pages;

use App\Filament\Resources\MaleResidentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateResident extends CreateRecord
{
    protected static string $resource = MaleResidentResource::class;

    protected function getActions(): array
    {
        return [

        ];
    }
}
