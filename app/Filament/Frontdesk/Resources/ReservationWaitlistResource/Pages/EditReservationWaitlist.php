<?php

namespace App\Filament\Frontdesk\Resources\ReservationWaitlistResource\Pages;

use App\Filament\Frontdesk\Resources\ReservationWaitlistResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReservationWaitlist extends EditRecord
{
    protected static string $resource = ReservationWaitlistResource::class;

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
