<?php

namespace App\Filament\Resources\MaleResidentResource\RelationManagers;

use App\Models\Visit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VisitsRelationManager extends RelationManager
{
    protected static string $relationship = 'visits';

    protected static ?string $title = 'الزيارات الخاصة بالمقييم';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('relative.name')->label('اسم الزائر'),

                TextColumn::make('type')->label('نوع الزيارة')->formatStateUsing(fn(Visit $visit) => Visit::TYPE[$visit->type]),

                TextColumn::make('duration')->label('مدة الزيارة')->formatStateUsing(fn(Visit $visit) => $visit->duration . ' ' . Visit::DURATION_TYPE[$visit->duration_type]),

                TextColumn::make('date_time')->label('تاريخ الزيارة'),

                TextColumn::make('companion_no')->label('عدد المرافقين'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
