<?php

namespace App\Filament\Resources\DriverResource\Pages;

use App\Filament\Resources\DriverResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDrivers extends ListRecords
{
    protected static string $resource = DriverResource::class;

    protected static ?string $title = 'Listado de Choferes';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Crear Chofer'),
        ];
    }
}
