<?php

namespace App\Filament\Pages;

use Carbon\Carbon;
use App\Models\Service;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\ExportBulkAction;
use App\Filament\Exports\DriversReportExporter;
use Filament\Tables\Concerns\InteractsWithTable;



class DriverReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Reportes';

    protected static ?string $title = 'Reporte de Choferes';

    protected static string $view = 'filament.pages.driver-report';

    public function table(Table $table): Table
    {
        return $table
            ->query(Service::query())
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('drivers.first_name')
                    ->getStateUsing(function ($record) {
                        if ($record->drivers->isEmpty()) {
                            return 'Sin Asignar';
                        } else {
                            return $record->drivers->pluck('full_name')->implode(', ');
                        }
                    })
                    ->label('Choferes')
                    ->listWithLineBreaks()
                    ->separator(',')
                    ->badge()
                    ->searchable(['first_name', 'last_name']),
                TextColumn::make('serviceCurrency.currency')
                    ->label('Modeda')
                    ->sortable()
                    ->default('Pendiente'),
                TextColumn::make('amount')
                    // ->getStateUsing(function ($record) {
                    //     return $record->amount ? $record->serviceCurrency->currency .' '. $record->amount : 'Pendiente';
                    // })
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->label('Monto')
                    ->sortable()
                    ->default('Pendiente'),
                TextColumn::make('pickup_date')->sortable()->label('Fecha')->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('pickup_time')->sortable()->label('Hora')->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('pickup_place')->sortable()->toggleable(isToggledHiddenByDefault: false)->label('Recogida'),
                TextColumn::make('dropoff_place')->sortable()->toggleable(isToggledHiddenByDefault: false)->label('Destino'),
                TextColumn::make('passengers')->sortable()->toggleable(isToggledHiddenByDefault: false)->label('Pasajeros'),
                TextColumn::make('order.company.business_name')->sortable()->toggleable(isToggledHiddenByDefault: false)->label('Compañía'),
                TextColumn::make('order.customer.full_name')->sortable()->toggleable(isToggledHiddenByDefault: false)->label('Cliente'),
            ])
            ->filters([
                Filter::make('Fecha Servicio')
                    ->form([
                        DatePicker::make('pickup_date_from')->label('Fecha Desde'),
                        DatePicker::make('pickup_date_until')->label('Fecha Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['pickup_date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('pickup_date', '>=', $date),
                            )
                            ->when(
                                $data['pickup_date_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('pickup_date', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['pickup_date_from'] ?? null) {
                            $indicators[] = Indicator::make('Fecha desde ' . Carbon::parse($data['pickup_date_from'])->toFormattedDateString())
                                ->removeField('pickup_date_from');
                        }

                        if ($data['pickup_date_until'] ?? null) {
                            $indicators[] = Indicator::make('Fecha hasta ' . Carbon::parse($data['pickup_date_until'])->toFormattedDateString())
                                ->removeField('pickup_date_until');
                        }

                        return $indicators;
                    })
            ])
            ->actions([
                // ...
            ])
            ->bulkActions([
                ExportBulkAction::make('Exportar Seleccionados')
                    ->label('Exportar Seleccionados')
                    ->exporter(DriversReportExporter::class)
            ]);
    }
}
