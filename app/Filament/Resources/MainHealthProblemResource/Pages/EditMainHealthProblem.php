<?php

namespace App\Filament\Resources\MainHealthProblemResource\Pages;

use App\Filament\Resources\MainHealthProblemResource;
use Filament\Actions\DeleteAction;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditMainHealthProblem extends EditRecord
{
    protected static string $resource = MainHealthProblemResource::class;

    protected function getRedirectUrl(): ?string
    {
        return self::$resource::getUrl('index');
    }

    protected function getActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
