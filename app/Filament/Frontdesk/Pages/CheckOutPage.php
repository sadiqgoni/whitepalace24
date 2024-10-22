<?php

namespace App\Filament\Frontdesk\Pages;

use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use App\Models\CheckIn;
use App\Models\CheckOut;
use Filament\Notifications\Notification;

class CheckOutPage extends Page implements HasForms
{
    use InteractsWithForms;
    protected static string $view = 'filament.frontdesk.pages.checkout';
    protected static ?string $navigationGroup = 'Daily Operations';
    protected static ?string $navigationLabel = 'Check Out';
    protected static ?string $breadcrumb = 'Check Out Guest';
    protected static ?string $title = 'Check Out Guest';
    protected static ?int $navigationSort = 3;
    public ?array $data = [];
    public $selectedCheckIn;
    public $discountPercentage = 0;
    public $additionalCharges = 0;
    public $totalAmount = 0;
    public $discountAmount = 0;
    public $advancePayment = 0;
    public $dueAmount = 0;
    public $remainingAmount = 0;
    public $restaurantCharge = 0;
    public $lateCheckout = false;
    public $pricePerNight;

    public $laundryCharge;
    public $carHireCharge;

    public $payableAmount;
    public $changeAmount;
    public $amountPaying;


    public function mount()
    {
        $this->form->fill();

    }
    // Form for selecting guest and room
    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('check_in_id')
                ->label('Select Guest and Room')
                ->options(
                    CheckIn::where('booking_status', 'Checked In')
                        ->pluck('room_number', 'id')
                        ->map(fn($room_number, $id) => "{$this->getGuestInfo($id, $room_number)}")
                )
                ->reactive()
                ->afterStateUpdated(fn($state) => $this->updateSelectedCheckIn($state))
                ->searchable()
                ->placeholder('Select a guest by name or room')
                ->required(),
        ])
            ->columns(2)
            ->statePath('data');
    }

    // Helper function to get guest info
    private function getGuestInfo($id, $room_number)
    {
        $checkIn = CheckIn::find($id);
        return "{$checkIn->guest_name} - Room {$room_number}";
    }

    // Update the selected check-in details
    public function updateSelectedCheckIn($checkInId)
    {
        $this->selectedCheckIn = CheckIn::find($checkInId);
        $this->restaurantCharge = $this->selectedCheckIn->restaurant_bill ?? 0;
        $this->advancePayment = $this->selectedCheckIn->paid_amount ?? 0;
    }

    // Perform the checkout action
    public function checkOut()
    {
        // Create new checkout record
        $checkOut = $this->createCheckOutRecord();

        // Notify user of success
        Notification::make()
            ->title('Checkout Successful')
            ->body('The guest has been successfully checked out.')
            ->success()
            ->send();
    }



    private function createCheckOutRecord()
    {
        return CheckOut::create([
            'check_in_id' => $this->selectedCheckIn->id,
            'guest_name' => $this->selectedCheckIn->guest_name,
            'room_number' => $this->selectedCheckIn->room_number,
            'check_in_time' => $this->selectedCheckIn->check_in_time,
            'check_out_time' => now(),
            'price_per_night' => $this->pricePerNight,
            'total_amount' => $this->totalAmount,
            'advance_payment' => $this->advancePayment,
            'restaurant_charge' => $this->restaurantCharge,
            'laundry_charge' => $this->laundryCharge,
            'car_hire_charge' => $this->carHireCharge,
            'additional_charges' => $this->additionalCharges,
            'discount_percentage' => $this->discountPercentage,
            'discount_amount' => $this->discountAmount,
            'due_amount' => $this->dueAmount,
            'payable_amount' => $this->payableAmount,
            'remaining_amount' => $this->remainingAmount,
            'change_amount' => $this->changeAmount,
            'amount_paying' => $this->amountPaying,
        ]);
    }
}
