<?php

namespace App\Filament\Frontdesk\Resources\CarRentalResource\Pages;

use App\Filament\Frontdesk\Resources\CarRentalResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCarRental extends ViewRecord
{
    protected static string $resource = CarRentalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
