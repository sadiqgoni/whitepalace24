<?php

namespace App\Filament\Frontdesk\Widgets;

use App\Models\CheckIn;
use App\Models\CheckOut;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Reservation;
use Filament\Widgets\StatsOverviewWidget\Card;


class StatsOverview extends BaseWidget
{
    public $activeCard = 'active';  // Default active card
    protected static ?string $pollingInterval = '5s';

    // Method to update active card
    public function setActiveCard($card)
    {
        $this->activeCard = $card;
    }

    // Method to generate common card styles
    protected function getCardStyles(string $cardType): array
    {
        $isActive = $this->activeCard === $cardType;

        return [
            'style' => $isActive
                ? 'background-color: #ffffff; color: #007bff; border-radius: 12px; border: 2px solid #007bff; box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);'
                : 'background-color: #ffffff; color: #333; border-radius: 12px; border: 3px solid #28a745; box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);',
            'class' => 'cursor-pointer transition duration-200 ease-in-out transform hover:scale-105 hover:shadow-lg',
            'wire:click' => "\$set('activeCard', '$cardType')",
        ];
    }

    // Method to generate card components
    protected function createCard(string $title, int $count, string $description, string $icon, string $cardType, string $event): Card
    {
        return Card::make($title, $count)
            ->description($description)
            ->descriptionIcon($icon)
            ->color($this->activeCard === $cardType ? '' : 'success')
            ->extraAttributes(array_merge($this->getCardStyles($cardType), [
                'x-on:click' => "\$dispatch('$event')",
            ]));
    }

    // Get statistics array
    protected function getStats(): array
    {
        return [
            $this->getCheckedIn(),
            $this->getCheckedOut(),
            $this->getConfirmedReservations(),
        ];
    }


    protected function getCheckedIn(): Card
    {
        $count = CheckIn::count();  // Count of checked-in guests
        return $this->createCard(
            'Checked-In Guests',    // Title
            $count,                 // Number of guests
            'Guests currently checked in',  // Description
            'heroicon-o-calendar',   // Icon (ensure it's correct)
            'active',                // Status or tag
            'showCheckedInGuestsTable' // Action or view
        );
    }
    

    // Card for Checked-Out Reservationsprotected function getCheckedOut(): Card
protected function getCheckedOut(): Card
{
    $count = CheckOut::count();  // Count of checked-out guests
    return $this->createCard(
        'Checked-Out Guests',     // Title
        $count,                   // Number of guests
        'Guests who have checked out',  // Description
        'heroicon-o-check',       // Icon (ensure it's correct)
        'checkedOut',             // Status or tag
        'showCheckedOutGuestsTable' // Action or view
    );
}

    // Card for Confirmed Reservations
    protected function getConfirmedReservations(): Card
    {
        $count = Reservation::where('status', 'Confirmed')->count();
        return $this->createCard('Confirmed Reservations', $count, 'Confirmed Reservations', 'heroicon-o-calendar-days', 'confirmedReservation', 'showConfirmedReservationTable');
    }
}
