<?php

namespace App\Filament\Management\Resources\FoodDivisionResource\Pages;

use App\Filament\Management\Resources\FoodDivisionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFoodDivisions extends ListRecords
{
    protected static string $resource = FoodDivisionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Add New Cuisine Category')

        ];
    }
}
