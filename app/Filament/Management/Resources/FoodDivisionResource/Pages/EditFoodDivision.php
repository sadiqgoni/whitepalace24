<?php

namespace App\Filament\Management\Resources\FoodDivisionResource\Pages;

use App\Filament\Management\Resources\FoodDivisionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFoodDivision extends EditRecord
{
    protected static string $resource = FoodDivisionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function getRedirectUrl(): string{
        return $this->getResource()::getUrl('index');
    }
}
