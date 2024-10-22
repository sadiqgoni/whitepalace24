<?php
namespace App\Filament\Frontdesk\Resources\ReservationResource\Pages;

use App\Filament\Frontdesk\Resources\ReservationResource;
use App\Models\Coupon;
use App\Models\Guest;
use App\Models\Reservation;
use App\Models\Room;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateReservation extends CreateRecord
{
    protected static string $resource = ReservationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $reservation = $this->record; // Get the newly created reservation record

    
        // Fetch the coupon ID from the data
        $couponId = $reservation->coupon_id ?? null;

        if ($couponId) {
            // Find the coupon
            $coupon = Coupon::find($couponId);

            if ($coupon) {
                // Update the times_used count
                $coupon->increment('times_used');

                // Check if the usage limit is reached
                if ($coupon->times_used >= $coupon->usage_limit) {
                    // Deactivate the coupon
                    $coupon->update(['status' => 'inactive']);
                }
            }
        }

        // Handle guest stay count increment
        $guestId = $reservation->guest_id ?? null;

        if ($guestId) {
            $guest = Guest::find($guestId);

            if ($guest) {
                $guest->increment('stay_count'); // Increment the stay count
                $guest->save(); // Ensure the guest record is saved after incrementing
            }
        }

        // Mark the room as unavailable after reservation is confirmed
        $roomId = $reservation->room_id;

        if ($roomId) {
            $room = Room::find($roomId);
            if ($room) {
                $room->update(['status' => false]); // Mark room as unavailable
            }
        }

        // Perform any other actions needed after saving the reservation
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $lastReservation = Reservation::latest()->first();
        $newId = $lastReservation ? $lastReservation->id + 1 : 1;
    
        // Generate reservation number with leading zeros (e.g., #0001, #0002)
        $reservationNumber = '#' . str_pad($newId, 4, '0', STR_PAD_LEFT);
    
        // Assign the generated reservation number to the data array
        $data['reservation_number'] = $reservationNumber;

        // Clean the relevant fields before creation
        $fieldsToClean = ['total_amount', 'amount_paid', 'remaining_balance', 'price_per_night'];

        foreach ($fieldsToClean as $field) {
            if (isset($data[$field])) {
                // Clean the field by removing non-numeric characters except for periods (.)
                $cleanValue = preg_replace('/[^0-9.]/', '', $data[$field]);
                $data[$field] = (float) $cleanValue;
            }
        }

        return $data;
    }
}
