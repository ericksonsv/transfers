<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use pxlrbt\FilamentActivityLog\Pages\ListActivities;

class CustomerActivities extends ListActivities
{
    protected static string $resource = CustomerResource::class;
}
