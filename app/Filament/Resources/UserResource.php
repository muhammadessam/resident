<?php

namespace App\Filament\Resources;

use App\Enums\Permissions;
use App\Enums\Screens;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'المستخدمين';
    protected static ?int $navigationSort = 8;

    public static function getNavigationGroup(): ?string
    {
        return __('filament-spatie-backup::backup.pages.backups.navigation.group');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required()->maxLength(255)->label('الاسم التعريفي'),
                TextInput::make('user_name')->required()->maxLength(255)->label('اسم المستخدم'),
                Select::make('default_screen')->label('الشاشة الافتراضية')->options(array_column(Screens::cases(), 'value', 'name'))->live(),
                Select::make('permissions')
                    ->requiredIf('is_super_admin', false)
                    ->minItems(1)
                    ->multiple()
                    ->label('الصلاحيات')
                    ->options(array_column(Permissions::cases(), 'value', 'name'))->live(),
                TextInput::make('email')->email()->maxLength(255)->label('البريد الالكتروني'),
                Toggle::make('is_super_admin')
                    ->label('يمتك صلاحية السوبر ادمن')
                    ->columnSpan(2)->live()
                    ->afterStateUpdated(function ($state, Set $set) {
                        $set('permissions', null);
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(components: [
                TextColumn::make('name')->searchable()->label('الاسم التعريفي'),
                TextColumn::make('user_name')->searchable()->label('اسم المستخدم'),
                TextColumn::make('permissions')->badge()->label('الصلاحيات')->formatStateUsing(fn($state) => Permissions::{$state}->value),
                ToggleColumn::make('is_super_admin')->label('سوبر ادمن'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
