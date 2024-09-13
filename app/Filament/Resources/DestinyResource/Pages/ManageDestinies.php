<?php

namespace App\Filament\Resources\DestinyResource\Pages;

use App\Filament\Resources\DestinyResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageDestinies extends ManageRecords
{
    protected static string $resource = DestinyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
