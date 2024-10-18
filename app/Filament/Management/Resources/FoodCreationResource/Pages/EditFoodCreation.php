<?php

namespace App\Filament\Management\Resources\FoodCreationResource\Pages;

use App\Filament\Management\Resources\FoodCreationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFoodCreation extends EditRecord
{
    protected static string $resource = FoodCreationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function getRedirectUrl(): string{
        return $this->getResource()::getUrl('index');
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // $data['updated_by'] = auth()->id();
        $fieldsToClean = ['price',];
    
        foreach ($fieldsToClean as $field) {
            if (isset($data[$field])) {
                // Use regex to extract only numeric characters and decimals
                $cleanValue = preg_replace('/[^0-9.]/', '', $data[$field]);
                $data[$field] = (float) $cleanValue; // Convert the cleaned value to an integer
            }
        }
    
      
    
    
        return $data;
    }
}
