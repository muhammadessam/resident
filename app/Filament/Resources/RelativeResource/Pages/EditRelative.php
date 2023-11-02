<?php

namespace App\Filament\Resources\RelativeResource\Pages;

use App\Filament\Resources\RelativeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRelative extends EditRecord
{
    protected static string $resource = RelativeResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResource()::getUrl('index');

    }

    protected function getActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
