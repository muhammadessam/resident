<?php

namespace App\Filament\Pages;

use App\Enums\Permissions;
use App\Models\Resident;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class ResidentOutForHospital extends Page implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.resident-out-for-hospital';
    protected static ?string $navigationGroup = 'اخراج وعودة المقيم';

    protected ?string $heading = 'اخراج وعودة المقييم من المستشفي';

    protected static ?string $navigationLabel = 'اخراج وعودة المقيمم من المستشفي';

    public static function canAccess(): bool
    {
        return in_array(Permissions::RETURN_RESIDENT->name, filament()->auth()->user()->permissions ?? []) || filament()->auth()->user()->is_super_admin;
    }

    public function table(Table $table): Table
    {
        return $table->query(Resident::query()->where('is_out_to_hospital', '!=', null))
            ->columns([
                TextColumn::make('name')->label('اسم المقييم'),
                TextColumn::make('building')->label('المبني')->formatStateUsing(fn($state) => Resident::BUILDINGS[$state]),
                TextColumn::make('is_out_to_hospital')->date()->label('تاريخ الخروج'),
            ])->actions([
                Action::make('return')->label('ارجاع المقييم')
                    ->button()
                    ->icon('heroicon-s-backward')
                    ->color('success')
                    ->action(fn(Resident $record) => $record->update(['is_out_to_hospital' => null]))->requiresConfirmation(true)
            ]);
    }

}
