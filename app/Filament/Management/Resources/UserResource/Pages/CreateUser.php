<?php

namespace App\Filament\Management\Resources\UserResource\Pages;

use App\Filament\Management\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

use Filament\Notifications\Notification;
class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('New Staff Created');
    }
    protected function getRedirectUrl(): string{
        return $this->getResource()::getUrl('index');
    }
}
