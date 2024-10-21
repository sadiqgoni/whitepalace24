<?php
namespace App\Filament\Frontdesk\Resources\RoomResource\Pages;

use App\Filament\Frontdesk\Resources\RoomResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Models\RoomType; // Import your RoomType model
use Filament\Notifications\Notification; // For showing notifications

class ListRooms extends ListRecords
{
    protected static string $resource = RoomResource::class;

    protected function getHeaderActions(): array
    {
        $roomTypeExists = RoomType::exists();

        return [
            Actions\CreateAction::make()
                ->action(function () use ($roomTypeExists) {
                    if (!$roomTypeExists) {
                        Notification::make()
                            ->title('Action Required')
                            ->body('Please create a Room Type before adding a room.')
                            ->warning()
                            ->send();

                        // Stop the action from proceeding
                        $this->halt(); 
                    } else {
                        // Proceed with the normal create action
                        $this->redirect($this->getResource()::getUrl('create'));
                    }
                })
                ->tooltip($roomTypeExists ? null : 'Please create a Room Type before adding a room.'), // Tooltip when hovering
        ];
    }
}
