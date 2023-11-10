<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RelativeResource\Pages;
use App\Models\Relative;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RelativeResource extends Resource
{
    protected static ?string $model = Relative::class;

    protected static ?string $slug = 'relatives';

    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $label = 'قريب';
    protected static ?string $pluralLabel = 'الاقارب';
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?int $navigationSort = 4;


    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->required()->label('الاسم'),
            TextInput::make('id_number')->required()->unique('relatives', 'id_number', ignoreRecord: true)->label('رقم الهوية'),
            TextInput::make('phone1')->required()->label('الهاتف 1'),
            TextInput::make('phone2')->label('الهاتف 2'),
            TextInput::make('phone3')->label('الهاتف 3'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('id_number')->searchable(),
            TextColumn::make('phone1')->searchable(),
            TextColumn::make('phone2')->searchable(),
            TextColumn::make('phone3')->searchable(),
        ])->actions([
            ViewAction::make(), EditAction::make(), DeleteAction::make()
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRelatives::route('/'),
            'create' => Pages\CreateRelative::route('/create'),
            'edit' => Pages\EditRelative::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }
}
