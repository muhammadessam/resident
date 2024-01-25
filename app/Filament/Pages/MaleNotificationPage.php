<?php

namespace App\Filament\Pages;

use ArPHP\I18N\Arabic;
use Filament\Pages\Page;

class MaleNotificationPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.male-notification-page';

    protected static ?string $title = 'شاشة اشعارات قسم الذكور';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'شاشة الاشعارات';


}
