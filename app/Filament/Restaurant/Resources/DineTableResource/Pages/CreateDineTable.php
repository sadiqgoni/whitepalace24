<?php

namespace App\Filament\Restaurant\Resources\DineTableResource\Pages;

use App\Filament\Restaurant\Resources\DineTableResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDineTable extends CreateRecord
{
    protected static string $resource = DineTableResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
