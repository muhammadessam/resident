<?php

namespace App\Filament\Pages;

use App\Exports\VisitsExportTemplate20;
use App\Models\Visit;
use App\TCPDFHelper\VisitTCPDF;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\HeaderActionsPosition;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Facades\Excel;
use setasign\Fpdi\Tcpdf\Fpdi;

class VisitsReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.visits-report';
    protected static ?string $title = 'تقرير الزيارات نموذج 20';
    protected static ?string $navigationGroup = 'التقارير';


    protected function table(Table $table): Table
    {
        return $table->query(Visit::query())
            ->columns([
                TextColumn::make('resident.name')->label('اسم المقييم'),
                TextColumn::make('resident.building')->label('المبني'),
                TextColumn::make('relative.name')->label('اسم الزائر'),
                TextColumn::make('relative.id_number')->label('رقم الهوية'),
                TextColumn::make('relative.phone1')->label('الهاتف'),
                TextColumn::make('companion_no')->label('عدد المرافقين'),
                TextColumn::make('date_time')->label('وقت الزيارة'),
                TextColumn::make('date_time_end')->label('وقت الخروج')->state(function (Visit $visit) {
                    return Carbon::parse($visit->date_time)->add($visit->duration_type, $visit->duration);
                }),
            ])->filters([
                Filter::make('date_time')
                    ->form([
                        DatePicker::make('from')->label('من: ')->inlineLabel()->displayFormat('d-m-Y'),
                        DatePicker::make('until')->label('الي: ')->inlineLabel()->displayFormat('d-m-Y'),
                    ])->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date_time', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date_time', '<=', $date),
                            );
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
                BulkAction::make('excel_report')->label('تصدير المحدد XSL')->action(function ($records) {
                    return Excel::download(new VisitsExportTemplate20($records), now() . '.xlsx');
                })
            ])->headerActions([
                Action::make('excel_report')
                    ->label('تصدير الكل XSL')
                    ->action(function () {
                        return Excel::download(new VisitsExportTemplate20($this->getFilteredTableQuery()->get()), now() . '.xlsx');
                    })
            ])->headerActionsPosition(HeaderActionsPosition::Bottom);
    }
}
