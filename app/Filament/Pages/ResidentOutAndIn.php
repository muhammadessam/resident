<?php

namespace App\Filament\Pages;

use App\Models\Visit;
use Carbon\CarbonInterval;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class ResidentOutAndIn extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.resident-out-and-in';
    protected static ?string $title = 'اخراج وعودة المقيم';
    protected static ?string $navigationLabel = 'اخراج وعودة المقييم';
    protected static ?int $navigationSort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('لا يوجد مقيميين في زيارات خارجية')
            ->query(Visit::query()->where('type', 'external')->whereTime('end_date', '>=', now()))
            ->columns([
                TextColumn::make('resident.name')->label('اسم المقييم'),
                TextColumn::make('relative.name')->label('اسم القريب'),
                TextColumn::make('duration')->label('مدة الزيارة')->formatStateUsing(fn($record) => CarbonInterval::make($record->duration, $record->duration_type)->forHumans()),
                TextColumn::make('date_time')->label('تاريخ الزيارة')->date('Y-m-d'),
                TextColumn::make('time')->state(fn(Visit $record) => $record->date_time)->time('h:i A')->label('وقت الزيارة'),
                TextColumn::make('end_date')->label('تاريخ العودة')->date('Y-m-d'),
            ])->actions([
                Action::make('return_now')
                    ->label('عودة الان')
                    ->requiresConfirmation(true)
                    ->action(function ($record) {
                        $record->update([
                            'end_date' => now()
                        ]);
                    })->button()->color('success'),
                Action::make('return')
                    ->label('عودة')
                    ->form([
                        DateTimePicker::make('end_date')->label('ارجاع المقييم بتاريخ :')->maxDate(now())->default(now()),
                    ])->action(function (Visit $record, $data) {
                        $record->update($data);
                    })->button()->color('info'),
                Action::make('extend')
                    ->label('تمديد')
                    ->form([
                        TextInput::make('duration')
                            ->type('number')
                            ->label('المدة')
                            ->inlineLabel()
                            ->live()
                            ->required()
                            ->default(fn($record) => $record->duration)
                            ->suffix(function ($get) {
                                return $get('duration') > 10 ? 'يوماً' : 'ايام';
                            }),
                        Select::make('duration_type')
                            ->label('المدة بالايام ام الساعات')
                            ->required()
                            ->options([
                                'days' => 'يوم',
                                'hours' => 'ساعة',
                            ])->default(fn($record) => $record->duration_type)->inlineLabel(),
                    ])->action(function ($record, $data) {
                        $record->update($data);
                    })->button()
                    ->color('danger')
            ]);
    }
}
