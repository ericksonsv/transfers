<?php

namespace App\Filament\Resources\ServiceStatusResource\Pages;

use App\Filament\Resources\ServiceStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageServiceStatuses extends ManageRecords
{
    protected static string $resource = ServiceStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
