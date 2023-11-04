<?php

namespace App\Filament\Resources\MaleResidentResource\Pages;

use App\Filament\Resources\MaleResidentResource;
use Filament\Actions;
use Filament\Infolists\Components\Section;
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
            Section::make('المعلومات الاساسية')->schema([
                TextEntry::make('name')->label('الاسم:')->inlineLabel(),
                TextEntry::make('number')->label('رقم المستفيد:')->inlineLabel(),
                TextEntry::make('building')->label('المبني:')->inlineLabel(),
                TextEntry::make('dob')->label('تاريخ الميلاد:')->date()->inlineLabel(),
                TextEntry::make('age')->label('العمر:')->formatStateUsing(fn(string $state) => $state . ' سنوات ')->inlineLabel(),
                TextEntry::make('doe')->label('تاريخ الانضمام:')->date()->inlineLabel(),
                TextEntry::make('doe')->label('تاريخ الانضمام منذ:')->date()->since()->inlineLabel(),
                TextEntry::make('external_visit_authorized')->label('المصرح لهم بالزيارة الخارجية:')->inlineLabel(),
                TextEntry::make('internal_visit_authorized')->label('المصرح لهم بالزيارة الداخلية:')->inlineLabel(),
            ])->columns(2),

            Section::make('المرفقات')->schema([
                SpatieMediaLibraryImageEntry::make('visit_allow_report')->label('استمارة تصريح الزيارة:')->collection('visit_allow_report'),
                SpatieMediaLibraryImageEntry::make('uploads')->label('مرفقات اخري:')->collection('uploads'),
            ])->columns(2),

        ]);
    }
}
