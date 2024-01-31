<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use ShuvroRoy\FilamentSpatieLaravelBackup\Pages\Backups as BaseBackups;

class BackUps extends BaseBackups
{
    protected static ?string $navigationGroup = 'الاعدادت';

    public static function getNavigationGroup(): ?string
    {
        return 'الاعدادت';
    }

    public static function canAccess(): bool
    {
        return filament()->auth()->user()->is_super_admin;
    }
}
