<?php

namespace App\Filament\Frontdesk\Resources\RoomResource\Pages;

use App\Filament\Frontdesk\Resources\RoomResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRoom extends ViewRecord
{
    protected static string $resource = RoomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
