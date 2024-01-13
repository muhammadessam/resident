<?php

namespace App\Filament\Resources\VisitResource\Pages;

use App\Filament\Resources\VisitResource;
use App\Models\Visit;
use Carbon\CarbonInterval;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\FontWeight;

class ViewVisit extends ViewRecord
{
    protected static string $resource = VisitResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('معلومات الزيارة')
                ->schema([
                    TextEntry::make('resident.number')->label('رقم المستفيد')->inlineLabel()->weight(FontWeight::Bold),
                    TextEntry::make('resident.name')->label('اسم المقييم')->inlineLabel()->weight(FontWeight::Bold),
                    TextEntry::make('relative.name')->label('اسم القريب')->inlineLabel()->weight(FontWeight::Bold),
                    TextEntry::make('relative.id_number')->label('رقم الهوية')->inlineLabel()->weight(FontWeight::Bold),
                    TextEntry::make('relative.phone1')->label('رقم الجوال')->inlineLabel()->weight(FontWeight::Bold),
                    TextEntry::make('type')->label('نوع الزيارة')->formatStateUsing(fn($state) => Visit::TYPE[$state] ?? '')->inlineLabel()->weight(FontWeight::Bold),
                    TextEntry::make('resident.name')->label('اسم المقييم')->inlineLabel()->weight(FontWeight::Bold),
                    TextEntry::make('date_time')->label('تاريخ الزيارة')->date('Y-m-d')->inlineLabel()->weight(FontWeight::Bold),
                    TextEntry::make('time')->state(fn(Visit $record) => $record->date_time)->time('h:i A')->label('وقت الزيارة')->inlineLabel()->weight(FontWeight::Bold),
                    TextEntry::make('duration')->label('مدة الزيارة')->inlineLabel()->weight(FontWeight::Bold)->formatStateUsing(fn($record) => CarbonInterval::make($record->duration, $record->duration_type)->forHumans()),
                    TextEntry::make('end_date')->label('تاريخ العودة')->date('Y-m-d')->weight(FontWeight::Bold)->inlineLabel(),
                    TextEntry::make('createdBy.name')->label('تم الاضافة بواسطة')->inlineLabel()->weight(FontWeight::Bold),
                ])->columns(2),

            Section::make('معلومات المقييم')
                ->schema([
                    TextEntry::make('resident.name')->label('الاسم:')->inlineLabel()->weight(FontWeight::Bold),
                    TextEntry::make('resident.building')->label('المبني:')->inlineLabel()->weight(FontWeight::Bold),
                    TextEntry::make('resident.external_visit_authorized')->label('المصرح لهم بالزيارة الخارجية:')->inlineLabel()->weight(FontWeight::Bold),
                    TextEntry::make('resident.internal_visit_authorized')->label('المصرح لهم بالزيارة الداخلية:')->inlineLabel()->weight(FontWeight::Bold),
                    TextEntry::make('resident.notes')->label('ملاحظات')->inlineLabel()->weight(FontWeight::Bold),
                ])->columnSpan(1),

            Section::make('معلومات القريب')
                ->schema([
                    TextEntry::make('relative.name')->label('الاسم:')->inlineLabel()->weight(FontWeight::Bold),
                    TextEntry::make('relative.id_number')->label('رقم الهوية:')->inlineLabel()->weight(FontWeight::Bold),
                    TextEntry::make('relative.phone1')->label('رقم الجوال')->inlineLabel()->weight(FontWeight::Bold),
                    TextEntry::make('relative.phone2')->label('رقم الجوال')->inlineLabel()->weight(FontWeight::Bold),
                    TextEntry::make('relative.phone3')->label('رقم الجوال')->inlineLabel()->weight(FontWeight::Bold),
                ])->columnSpan(1),
        ])->columns(2);
    }
}
