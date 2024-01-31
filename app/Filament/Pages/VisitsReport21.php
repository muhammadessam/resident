<?php

namespace App\Filament\Pages;

use App\Enums\Permissions;
use App\Exports\VisitsExportTemplate20;
use App\Exports\VisitsExportTemplate21;
use App\Models\Resident;
use App\Tables\Columns\ExternalVisitsDurationSum;
use Filament\Forms\Components\DatePicker;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\HeaderActionsPosition;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;

class VisitsReport21 extends Page implements HasTable, HasInfolists
{

    use InteractsWithTable, InteractsWithInfolists;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.visits-report21';
    protected static ?string $navigationGroup = 'التقارير';

    protected static ?string $title = 'تقرير الزيارات نموذج 21';

    public static function canAccess(): bool
    {
        return in_array(Permissions::VIEW_REPORTS->name, filament()->auth()->user()->permissions ?? []) || filament()->auth()->user()->is_super_admin;
    }

    public function table(Table $table): Table
    {
        return $table->query(Resident::query())->columns([
            TextColumn::make('name')->label('اسم المقييم')->sortable(),
            TextColumn::make('internal_visits_count')->label('عدد الزيارات الداخلية')->counts('internalVisits'),
            TextColumn::make('external_visits_count')->label('عدد الزيارات الخارجية')->counts('externalVisits'),
            ExternalVisitsDurationSum::make('external_duration_counts')->label('مدة الزيارات الخارجية'),
            TextColumn::make('lastVisit.date_time')->date(),
        ])->filters([
            SelectFilter::make('type')
                ->label('القسم')
                ->options(Resident::TYPE),

            Filter::make('date_time')
                ->form([
                    DatePicker::make('from')->label('من: ')->inlineLabel()->displayFormat('d-m-Y'),
                    DatePicker::make('until')->label('الي: ')->inlineLabel()->displayFormat('d-m-Y'),
                ])->query(function (Builder $query, array $data): Builder {
                    return
                        $query->when($data['from'], function (Builder $query, $date) {
                            return $query->whereHas('visits', function (Builder $builder) use ($date) {
                                $builder->whereDate('date_time', '>=', $date);
                            });
                        })->when($data['until'], function (Builder $query, $date) {
                            return $query->whereHas('visits', function (Builder $builder) use ($date) {
                                $builder->whereDate('date_time', '<=', $date);
                            });
                        });
                })->indicateUsing(function (array $data) {
                    $indicator = null;
                    if ($data['from']) {
                        $indicator = 'تاريخ الزيارة من: ' . $data['from'];
                    }
                    if ($data['until']) {
                        $indicator .= ' الي: ' . $data['until'];
                    }
                    return $indicator;
                })

        ])->headerActions([
            Action::make('excel_report')
                ->label('تصدير الكل XSL')
                ->action(function () {
                    return Excel::download(new VisitsExportTemplate21($this->getFilteredTableQuery()->get()), now() . '.xlsx');
                })
        ])->headerActionsPosition(HeaderActionsPosition::Bottom);
    }

    public function countInfolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->state([
                'internal_visits_count' => function () {
                    return $this->getFilteredTableQuery()->withCount('internalVisits')->get()->sum('internal_visits_count');
                },

                'external_visits_count' => function () {
                    return $this->getFilteredTableQuery()->withCount('externalVisits')->get()->sum('external_visits_count');
                },
                'total_visits_count' => function () {
                    return $this->getFilteredTableQuery()->withCount('visits')->get()->sum('visits_count');
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
                    ->weight('bold')
            ]);
    }

}
