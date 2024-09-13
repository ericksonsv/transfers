<?php

namespace App\Filament\Resources\BusTechnicalSheetResource\Pages;

use App\Filament\Resources\BusTechnicalSheetResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageBusTechnicalSheets extends ManageRecords
{
    protected static string $resource = BusTechnicalSheetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
