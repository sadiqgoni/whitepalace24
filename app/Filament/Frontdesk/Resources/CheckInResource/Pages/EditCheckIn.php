<?php

namespace App\Filament\Frontdesk\Resources\CheckInResource\Pages;

use App\Filament\Frontdesk\Resources\CheckInResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCheckIn extends EditRecord
{
    protected static string $resource = CheckInResource::class;

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
