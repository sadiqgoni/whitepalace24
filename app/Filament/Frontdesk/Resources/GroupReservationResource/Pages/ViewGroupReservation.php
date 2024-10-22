<?php

namespace App\Filament\Frontdesk\Resources\GroupReservationResource\Pages;

use App\Filament\Frontdesk\Resources\GroupReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewGroupReservation extends ViewRecord
{
    protected static string $resource = GroupReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
