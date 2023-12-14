<?php

namespace App\Filament\Resources\VisitResource\Pages;

use App\Filament\Resources\VisitResource;
use App\Models\Relative;
use App\Models\RelativeResident;
use App\Models\Resident;
use App\Models\Visit;
use App\TCPDFHelper\VisitsForm;
use ArPHP\I18N\Arabic;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use JetBrains\PhpStorm\NoReturn;

class CreateVisit extends CreateRecord
{

    protected static string $resource = VisitResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if ($data['type'] == 'internal') {
            $data['duration_type'] = 'hours';
            $data['duration'] = 1;
        }
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getFormActions(): array
    {
        return array_merge([
            Action::make('pdf')->color('warning')->label('اضافة وطباعة')->icon('heroicon-o-printer')->action(function ($livewire) {
                $this->validate();
                $check_for_last_visit = Visit::where('resident_id', $livewire->data['resident_id'])
                    ->where('type', 'external')
                    ->whereDate('end_date', '>=', $livewire->data['date_time']);
                if ($check_for_last_visit?->count()) {
                    Notification::make()->danger()->title('المقيم في زيارة خارجية ')->body('عفواً هذا المقيم حالياً في زيارة خارجية')->persistent()->send();
                    $this->halt();
                }
                $visit = $this->handleRecordCreation($this->mutateFormDataBeforeCreate($livewire->data));
                $this->getCreatedNotification()->send();
                return redirect()->route('generate-visit-form', [
                    'visit' => $visit
                ]);
            })
        ], parent::getFormActions()
        );
    }

    protected function beforeCreate(): void
    {
        $check_for_last_visit = Visit::where('resident_id', $this->data['resident_id'])
            ->where('type', 'external')
            ->whereDate('end_date', '>=', $this->data['date_time']);

        if ($check_for_last_visit?->count()) {
            Notification::make()->danger()->title('المقيم في زيارة خارجية ')->body('عفواً هذا المقيم حالياً في زيارة خارجية')->persistent()->send();
            $this->halt();
        }
    }
}
