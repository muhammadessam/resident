<?php

namespace App\Filament\Resources\VisitResource\Pages;

use App\Filament\Resources\VisitResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVisit extends EditRecord
{
    protected static string $resource = VisitResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($data['type'] == 'internal') {
            $data['duration'] = null;
            $data['duration_type'] = null;
        }
        return $data;
    }

    protected function getActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
