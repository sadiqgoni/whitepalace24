<?php

namespace App\Filament\Frontdesk\Resources\CarRentalResource\Pages;

use App\Filament\Frontdesk\Resources\CarRentalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCarRental extends EditRecord
{
    protected static string $resource = CarRentalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string{
        return $this->getResource()::getUrl('index');
    }
}
