<?php

namespace App\Filament\Housekeeper\Resources\MaintenanceRequestResource\Pages;

use App\Filament\Housekeeper\Resources\MaintenanceRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMaintenanceRequest extends ViewRecord
{
    protected static string $resource = MaintenanceRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
