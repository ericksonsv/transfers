<?php

namespace App\Filament\Resources\ServiceCurrencyResource\Pages;

use App\Filament\Resources\ServiceCurrencyResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageServiceCurrencies extends ManageRecords
{
    protected static string $resource = ServiceCurrencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
