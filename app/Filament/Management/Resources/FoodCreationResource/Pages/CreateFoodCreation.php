<?php

namespace App\Filament\Management\Resources\FoodCreationResource\Pages;

use App\Filament\Management\Resources\FoodCreationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFoodCreation extends CreateRecord
{
    protected static string $resource = FoodCreationResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }



    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $fieldsToClean = ['price'];

        foreach ($fieldsToClean as $field) {
            if (isset($data[$field])) {
                $cleanValue = preg_replace('/[^0-9.]/', '', $data[$field]);
                $data[$field] = (float) $cleanValue;
            }

        }

        return $data;

    }

}

