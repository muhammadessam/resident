<?php

namespace App\Filament\Pages;

use App\Exports\ExclusiveReportExport;
use App\Models\Resident;
use App\TCPDFHelper\VisitTCPDF;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\HeaderActionsPosition;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;

class ExclusiveDptReport extends Page implements HasTable, HasInfolists
{
    use InteractsWithTable;
    use InteractsWithInfolists;


    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.exclusive-dpt-report';
    protected static ?string $navigationGroup = 'التقارير';
    protected static ?string $title = 'تقرير شامل للقسم';

    protected static ?string $navigationLabel = 'تقرير شامل للقسم';

    public function table(Table $table): Table
    {
        return $table
            ->query(Resident::query())
            ->columns([
                TextColumn::make('name')->label('اسم المقييم')->sortable(),
                TextColumn::make('internal_visits_count')->label('عدد الزيارات الداخلية')->counts('internalVisits'),
                TextColumn::make('external_visits_count')->label('عدد الزيارات الخارجية')->counts('externalVisits'),
                TextColumn::make('last_visit_date')
                    ->state(function (Resident $record) {
                        return $record->visits()->latest()->first()->date_time ?? '';
                    })
                    ->label('تاريخ اخر زيارة')
                    ->date('Y-m-d'),
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

            ])->bulkActions([
                BulkAction::make('selected_to_pdf')->label('تصدير المحدد PDF')->action(function ($records) {
                    $file_path = public_path(Carbon::now()->toDateString() . '.pdf');
                    $pdf = new VisitTCPDF();
                    $pdf->setupTableHeaders($this->table->getColumns());
                    $pdf->coloredTable($records, file_path: $file_path);
                    return response()->download($file_path)->deleteFileAfterSend();
                }),
                BulkAction::make('selected_to_excel')->label('تصدير المحدد EXCEL')->action(function ($records) {
                    return Excel::download(new ExclusiveReportExport($records), now() . '.xlsx',);
                }),
            ])->headerActions([
                Action::make('all_to_pdf')->label('تصدير الكل PDF')->action(function ($livewire) {
                    $file_path = public_path(Carbon::now()->toDateString() . '.pdf');
                    $pdf = new VisitTCPDF();
                    $pdf->setupTableHeaders($this->table->getColumns());
                    $pdf->coloredTable($this->getFilteredTableQuery()->with('visits')->get(), file_path: $file_path);
                    return response()->download($file_path)->deleteFileAfterSend();
                }),
                Action::make('all_to_excel')->label('تصدير الكل EXCEL')->action(function ($livewire) {
                    return Excel::download(new ExclusiveReportExport($this->getFilteredTableQuery()->get()), now()->toDateString() . '.xlsx');
                }),
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
