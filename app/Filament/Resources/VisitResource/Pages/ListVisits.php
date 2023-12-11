<?php

namespace App\Filament\Resources\VisitResource\Pages;

use App\Filament\Resources\VisitResource;
use App\Models\Visit;
use Exception;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ListVisits extends ListRecords
{
    protected static string $resource = VisitResource::class;

    /**
     * @throws Exception
     */
    public function table(Table $table): Table
    {
        return $table->columns([

            TextColumn::make('resident.name')->label('المقيم'),

            TextColumn::make('relative.name')->label('القريب'),

            TextColumn::make('type')->label('نوع الزيارة')->formatStateUsing(fn(Visit $visit) => Visit::TYPE[$visit->type]),

            TextColumn::make('duration')->label('مدة الزيارة')->formatStateUsing(fn(Visit $visit) => $visit->duration . ' ' . Visit::DURATION_TYPE[$visit->duration_type]),

            TextColumn::make('date_time')->label('وقت الزيارة'),

            TextColumn::make('companion_no')->label('عدد المرافقين'),
        ])->filters([
            SelectFilter::make('type')
                ->options(Visit::TYPE)
                ->label('نوع الزيارة'),
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
        ])->actions([
            Action::make('print')
                ->label('طباعة')
                ->icon('heroicon-o-printer')
                ->color('warning')
                ->url(fn(Visit $record) => route('generate-visit-form', $record))->openUrlInNewTab(),
            ViewAction::make(), EditAction::make(), DeleteAction::make(),
        ])->defaultSort('id', 'desc');
    }


    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
