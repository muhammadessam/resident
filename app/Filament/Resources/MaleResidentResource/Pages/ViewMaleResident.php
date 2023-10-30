<?php

namespace App\Filament\Resources\MaleResidentResource\Pages;

use App\Filament\Resources\MaleResidentResource;
use Filament\Actions;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewMaleResident extends ViewRecord
{
    protected static string $resource = MaleResidentResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            TextEntry::make('name')->label('الاسم:'),
            TextEntry::make('number')->label('رقم المستفيد:'),
            TextEntry::make('building')->label('المبني:'),
            TextEntry::make('dob')->label('تاريخ الميلاد:')->date(),
            TextEntry::make('age')->label('العمر:'),
            TextEntry::make('doe')->label('تاريخ الانضمام:')->date(),
            TextEntry::make('doe')->label('تاريخ الانضمام منذ:')->date()->since(),
            TextEntry::make('external_visit_authorized')->label('المصرح لهم بالزيارة الخارجية:'),
            TextEntry::make('internal_visit_authorized')->label('المصرح لهم بالزيارة الداخلية:'),
            SpatieMediaLibraryImageEntry::make('visit_allow_report')->label('استمارة تصريح الزيارة:')->collection('visit_allow_report'),
            SpatieMediaLibraryImageEntry::make('uploads')->label('مرفقات اخري:')->collection('uploads'),


        ]);
    }
}
