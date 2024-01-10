<?php

namespace App\Filament\Pages;

use App\Exports\RelativeResidentReportExport;
use App\Exports\VisitsExportTemplate20;
use App\Models\RelativeResident;
use App\Models\Resident;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Tables\Actions\HeaderActionsPosition;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Tag\B;

class RelativeResidentReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.relative-resident-report';
    protected static ?string $navigationGroup = 'التقارير';
    protected static ?string $title = 'تقارير الاقارب';
    protected static ?string $navigationLabel = 'تقارير الاقارب';
    protected static ?int $navigationSort = 5;

    public function table(Table $table): Table
    {
        return $table->query(RelativeResident::query())
            ->columns([
                TextColumn::make('resident.name')->label('اسم المقييم'),
                TextColumn::make('relative.name')->label('اسم المقييم'),
                TextColumn::make('relative.phone1')->label('رقم الجوال1'),
                TextColumn::make('relative.phone2')->label('رقم الجوال2'),
                TextColumn::make('relative.phone3')->label('رقم الجوال3'),
            ])->filters([
                SelectFilter::make('resident')
                    ->label('القسم')
                    ->options(Resident::TYPE)
                    ->modifyQueryUsing(function (Builder $query, $data) {
                        $query->when($data['value'] ?? false, function (Builder $query, $value) {
                            $query->whereHas('resident', function (Builder $query) use ($value) {
                                $query->where('type', $value);
                            });
                        });
                    })
            ])->headerActions([
                \Filament\Tables\Actions\Action::make('excel_report')
                    ->label('تصدير الكل XSL')
                    ->action(function () {
                        return Excel::download(new RelativeResidentReportExport($this->getFilteredTableQuery()->get()), now() . '.xlsx');
                    }),
            ])->headerActionsPosition(HeaderActionsPosition::Bottom);
    }
}
