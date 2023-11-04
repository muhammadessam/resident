<?php

namespace App\Filament\Resources\MaleResidentResource\RelationManagers;

use App\Models\Relative;
use App\Models\RelativeResident;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RelativesRelationManager extends RelationManager
{
    protected static string $relationship = 'relatives';
    protected static ?string $inverseRelationship = 'residents';
    protected static ?string $title = 'التكوين الاسري للمقيم';
    protected static ?string $label = 'قريب';
    protected static ?string $pluralLabel = 'الاقارب';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->required()->label('الاسم'),
            TextInput::make('id_number')->required()->unique('relatives', 'id_number', ignoreRecord: true)->label('رقم الهوية'),
            TextInput::make('phone1')->required()->label('الهاتف 1'),
            TextInput::make('phone2')->label('الهاتف 2'),
            TextInput::make('phone3')->label('الهاتف 3'),
            Select::make('relation')->label('صلة القرابة')
                ->options(RelativeResident::RELATION)
                ->live(),
            TextInput::make('other_relation')->label('صلةالقرابة')->hidden(fn(Get $get) => $get('relation') !== 'other')->live(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('id_number')->searchable(),
                TextColumn::make('phone1')->searchable(),
                TextColumn::make('phone2')->searchable(),
                TextColumn::make('phone3')->searchable(),
                TextColumn::make('relation')
                    ->label('صلة القرابة')
                    ->formatStateUsing(fn(string $state): string => $state == 'other' ? $state : RelativeResident::RELATION[$state])
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\AttachAction::make()->form(fn(Tables\Actions\AttachAction $action) => [
                    $action->getRecordSelect()->preload(),
                    Select::make('relation')->label('صلة القرابة')
                        ->options(RelativeResident::RELATION)
                        ->live(),
                    TextInput::make('other_relation')->label('صلةالقرابة')->hidden(fn(Get $get) => $get('relation') !== 'other')->live(),
                ]),
            ])
            ->actions([
//                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
//                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
