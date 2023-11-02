<?php

namespace App\Filament\Resources\RelativeResource\Pages;

use App\Filament\Resources\RelativeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRelatives extends ListRecords
{
    protected static string $resource = RelativeResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
