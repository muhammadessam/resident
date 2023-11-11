<?php

namespace App\Filament\Resources\MaleResidentResource\Pages;

use App\Filament\Resources\MaleResidentResource;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewMaleResident extends ViewRecord
{
    protected static string $resource = MaleResidentResource::class;

    protected function getFooterWidgets(): array
    {
        return [MaleResidentResource\Widgets\ResidentVisitsChart::class];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('المعلومات الاساسية')->schema(components: [
                TextEntry::make('name')->label('الاسم:')->inlineLabel(),
                TextEntry::make('number')->label('رقم المستفيد:')->inlineLabel(),
                TextEntry::make('building')->label('المبني:')->inlineLabel(),
                TextEntry::make('dob')->label('تاريخ الميلاد:')->formatStateUsing(fn(string $state) => Carbon::parse($state)->toDateString() . ' العمر ' . Carbon::parse($state)->age . ' سنة ')->inlineLabel(),
                TextEntry::make('doe')->label('تاريخ الانضمام:')->formatStateUsing(fn(string $state) => Carbon::parse($state)->toDateString() . ' ' . Carbon::parse($state)->since())->inlineLabel(),
                TextEntry::make('city.name')->label('المدينة')->inlineLabel(),
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
