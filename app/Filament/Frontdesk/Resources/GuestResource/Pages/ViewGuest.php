<?php

namespace App\Filament\Frontdesk\Resources\GuestResource\Pages;

use App\Filament\Frontdesk\Resources\GuestResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewGuest extends ViewRecord
{
    protected static string $resource = GuestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
