<?php

namespace App\Filament\Restaurant\Resources\DineTableResource\Pages;

use App\Filament\Restaurant\Resources\DineTableResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDineTable extends EditRecord
{
    protected static string $resource = DineTableResource::class;
    protected function getRedirectUrl(): string{
        return $this->getResource()::getUrl('index');
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
