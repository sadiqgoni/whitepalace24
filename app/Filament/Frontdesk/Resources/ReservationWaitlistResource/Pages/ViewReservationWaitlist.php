<?php

namespace App\Filament\Frontdesk\Resources\ReservationWaitlistResource\Pages;

use App\Filament\Frontdesk\Resources\ReservationWaitlistResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewReservationWaitlist extends ViewRecord
{
    protected static string $resource = ReservationWaitlistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
