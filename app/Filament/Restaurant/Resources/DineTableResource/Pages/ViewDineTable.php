<?php

namespace App\Filament\Restaurant\Resources\DineTableResource\Pages;

use App\Filament\Restaurant\Resources\DineTableResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDineTable extends ViewRecord
{
    protected static string $resource = DineTableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
