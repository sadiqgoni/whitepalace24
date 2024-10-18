<?php

namespace App\Filament\Management\Resources\FoodDivisionResource\Pages;

use App\Filament\Management\Resources\FoodDivisionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFoodDivision extends CreateRecord
{
    protected static string $resource = FoodDivisionResource::class;
    protected function getRedirectUrl(): string{
        return $this->getResource()::getUrl('index');
    }
}
