<?php

namespace App\Filament\Frontdesk\Resources\RoomTypeResource\Pages;

use App\Filament\Frontdesk\Resources\RoomTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRoomType extends ViewRecord
{
    protected static string $resource = RoomTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
