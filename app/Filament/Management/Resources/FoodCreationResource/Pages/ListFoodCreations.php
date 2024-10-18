<?php

namespace App\Filament\Management\Resources\FoodCreationResource\Pages;

use App\Filament\Management\Resources\FoodCreationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFoodCreations extends ListRecords
{
    protected static string $resource = FoodCreationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
