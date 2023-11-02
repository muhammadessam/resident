<?php

namespace App\Filament\Resources\RelativeResource\Pages;

use App\Filament\Resources\RelativeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRelative extends CreateRecord
{
    protected static string $resource = RelativeResource::class;

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
