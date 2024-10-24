<?php

namespace App\Filament\Management\Resources\UserResource\Pages;

use App\Filament\Management\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Add New User')

        ];
    }
}
