<?php

namespace App\Filament\Resources\MainHealthProblemResource\Pages;

use App\Filament\Resources\MainHealthProblemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMainHealthProblems extends ListRecords
{
    protected static string $resource = MainHealthProblemResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
