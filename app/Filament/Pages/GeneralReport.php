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
use Filament\Forms\Components\TextInput;
use Filament\Tables\Concerns\InteractsWithTable;



class GeneralReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Reportes';

    protected static ?string $title = 'Reporte General';

    protected static string $view = 'filament.pages.general-report';

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
                TextColumn::make('shiftz')->label('Turno'),
                TextColumn::make('client')->label('Pasajero'),
            ])
            ->filters([
                Filter::make('order.company.business_name')
                ->label('Nombre de Empresa')
                ->form([
                    TextInput::make('business_name')
                        ->label('Compañía'),
                ])
                ->query(function ($query, array $data) {
                    if (! $data['business_name']) {
                        return $query;
                    }

                    return $query->whereHas('order.company', function (Builder $q) use ($data) {
                        $q->where('business_name', 'like', '%' . $data['business_name'] . '%');
                    });
                }),

                Filter::make('shiftz')
                ->form([
                    TextInput::make('shiftz')->label('Turno'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['shiftz'],
                            fn (Builder $query): Builder => $query->where('shiftz', 'like', '%'.$data['shiftz'].'%'),
                        );
                }),

                Filter::make('client')
                ->form([
                    TextInput::make('client')->label('Pasajero'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['client'],
                            fn (Builder $query): Builder => $query->where('client', 'like', '%'.$data['client'].'%'),
                        );
                }),

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
