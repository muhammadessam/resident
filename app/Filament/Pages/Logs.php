<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;

class Logs extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.logs';
    protected static ?string $title = 'السجل';

    public static function getNavigationGroup(): ?string
    {
        return __('filament-spatie-backup::backup.pages.backups.navigation.group');
    }

    public function table(Table $table): Table
    {
        return $table->query(Activity::query())->columns([
            TextColumn::make('description')->label('وصف السجل'),
            TextColumn::make('causer.name')->label('بواسطة'),
            TextColumn::make('properties')->label('بتاريخ')
        ])->headerActions([
            Action::make('delete-all')->label('حذف الكل')->requiresConfirmation()->action(fn() => Activity::truncate())->color('danger')->button()->icon('heroicon-s-trash'),
        ])->actions([
            Action::make('delete')->label('حذف السجل')->requiresConfirmation()->action(fn($record) => $record->delete())->color('danger')->button()->icon('heroicon-s-trash'),
        ]);
    }
}
