<?php

namespace App\Filament\Resources\MaleResidentResource\Pages;

use App\Filament\Resources\MaleResidentResource;
use App\Models\RelativeResident;
use App\Models\Visit;
use Filament\Forms\Components\DatePicker;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ResidentVisitsReport extends Page implements HasTable, HasInfolists
{
    use InteractsWithRecord, InteractsWithTable, InteractsWithInfolists;

    protected static string $resource = MaleResidentResource::class;

    protected static string $view = 'filament.resources.male-resident-resource.pages.resident-visits-report';

    protected static ?string $title = 'تقرير زيارات';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
        static::authorizeResourceAccess();
    }

    public function table(Table $table)
    {
        return $table
            ->query(Visit::query()->where('resident_id', $this->record->id))
            ->columns([
                TextColumn::make('relative.name')->label('اسم الزائر'),

                TextColumn::make('relative_relation')
                    ->label('صلة القرابة')
                    ->getStateUsing(function ($record) {
                        $relation = RelativeResident::where('resident_id', $record->resident_id)
                            ->where('relative_id', $record->relative_id)->first()->relation;
                        return array_key_exists($relation, RelativeResident::RELATION) ? RelativeResident::RELATION[$relation] : $relation;
                    }),

                TextColumn::make('type')->label('نوع الزيارة')->formatStateUsing(fn(Visit $visit) => Visit::TYPE[$visit->type]),

                TextColumn::make('duration')->label('مدة الزيارة')->formatStateUsing(fn(Visit $visit) => $visit->duration . ' ' . Visit::DURATION_TYPE[$visit->duration_type]),

                TextColumn::make('date_time')->label('تاريخ الزيارة')->date('Y-m-d'),

                TextColumn::make('time')->state(fn(Visit $record) => $record->date_time)->time('h:i A')->label('وقت الزيارة'),

                TextColumn::make('id')->label('الرقم المرجعي')->formatStateUsing(fn($record) => str_pad($record->id, 5, '0', STR_PAD_LEFT)),
            ])->filters([
                Filter::make('date_time')
                    ->form([
                        DatePicker::make('start')->label('بداية من: ')->format('d-m-Y')->inlineLabel(true),
                        DatePicker::make('end')->label('نهاية الي: ')->format('d-m-Y')->inlineLabel(true),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['start'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date_time', '>=', $date),
                            )
                            ->when(
                                $data['end'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date_time', '<=', $date),
                            );
                    })->indicateUsing(function (array $data): ?string {
                        $indicator = null;
                        if ($data['start']) {
                            $indicator = 'بداية من تاريخ: ' . $data['start'];
                        }
                        if ($data['end']) {
                            $indicator .= ' الي تاريخ: ' . $data['end'];
                        }
                        return $indicator;
                    })
            ]);
    }

    public function visitInfoList(Infolist $infolist)
    {
        return $infolist
            ->record($this->record)->schema([
                TextEntry::make('name')->label('اسم المقيمم')->inlineLabel(),

                TextEntry::make('dob')->label('تاريخ الميلاد')->inlineLabel()->date('Y-m-d'),

                TextEntry::make('age')->label('العمر')->inlineLabel(),

                TextEntry::make('doe')->label('تاريخ الدخول')->inlineLabel()->date()->since(),

                TextEntry::make('building')->label('المبني')->inlineLabel(),

            ]);
    }

    public function countInfolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->state([
                'internal_visits_count' => function () {
                    return $this->record->internalVisits()->count();
                },

                'external_visits_count' => function () {
                    return $this->record->externalVisits()->count();
                },
                'total_visits_count' => function () {
                    return $this->record->visits()->count();
                },
            ])
            ->schema([
                TextEntry::make('internal_visits_count')
                    ->label('عددالزيارات الداخلية')
                    ->inlineLabel()
                    ->weight('bold'),
                TextEntry::make('external_visits_count')
                    ->label('عددالزيارات الخارجية')
                    ->inlineLabel()
                    ->weight('bold'),
                TextEntry::make('total_visits_count')
                    ->label('عددالزيارات الكلية')
                    ->inlineLabel()
                    ->weight('bold'),
            ]);
    }

}
