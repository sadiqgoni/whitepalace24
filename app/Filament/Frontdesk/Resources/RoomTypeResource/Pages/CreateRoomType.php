<?php

namespace App\Filament\Frontdesk\Resources\RoomTypeResource\Pages;

use App\Filament\Frontdesk\Resources\RoomTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRoomType extends CreateRecord
{
    protected static string $resource = RoomTypeResource::class;

    protected function getRedirectUrl(): string{
        return $this->getResource()::getUrl('index');
    }
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $fieldsToClean = ['base_price'];

        foreach ($fieldsToClean as $field) {
            if (isset($data[$field])) {
                $cleanValue = preg_replace('/[^0-9.]/', '', $data[$field]);
                $data[$field] = (float) $cleanValue;
            }
        }

        return $data;

    }
}
