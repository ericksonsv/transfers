<?php

namespace App\Filament\Exports;

use App\Models\Service;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class OperationReportExporter extends Exporter
{
    protected static ?string $model = Service::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('client')->label('Cliente'),
            ExportColumn::make('drivers')
                ->getStateUsing(function ($record) {
                    if ($record->drivers->isEmpty()) {
                        return 'Sin Asignar';
                    } else {
                        return $record->drivers->pluck('full_name')->implode(', ');
                    }
                })
                ->label('Choferes'),
            ExportColumn::make('pickup_date')->label('Fecha'),
            ExportColumn::make('pickup_time')->label('Hora'),
            ExportColumn::make('pickup_place')->label('Recogida'),
            ExportColumn::make('dropoff_place')->label('Destino'),
            ExportColumn::make('passengers')->label('Pasajeros'),
            ExportColumn::make('flight_number')->label('No. Vuelo')->default('N/A'),
            ExportColumn::make('flight_time')->label('Hora Vuelo')->default('N/A'),
            ExportColumn::make('order.company.business_name')->label('Compañía'),
            ExportColumn::make('order.customer.full_name')->label('Cliente'),
            ExportColumn::make('note')->label('Nota')->default('N/A'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your operation report export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
