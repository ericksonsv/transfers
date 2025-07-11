<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected static ?string $title = 'Listado de Órdenes';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Crear Orden'),
        ];
    }
}
