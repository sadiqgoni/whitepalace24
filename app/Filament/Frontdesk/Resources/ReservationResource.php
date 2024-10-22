<?php

namespace App\Filament\Frontdesk\Resources;

use App\Filament\Frontdesk\Resources\ReservationResource\Pages;
use App\Filament\Frontdesk\Resources\ReservationResource\RelationManagers;
use App\Models\CheckIn;
use App\Models\Coupon;
use App\Models\Reservation;
use App\Models\Guest;
use App\Models\Room;
use Filament\Facades\Filament;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\Actions\Action;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard';
    protected static ?string $navigationGroup = 'Daily Operations';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Reservation';


    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Wizard::make([
                // Step 1: Guest Information
                Step::make('Guest Information')
                    ->icon('heroicon-o-user')
                    ->description('Enter guest details or select an existing guest.')
                    ->completedIcon('heroicon-m-check-circle')
                    ->schema([
                        Select::make('guest_id')
                            ->label('Select Guest')
                            ->preload()
                            ->searchable()
                            ->options(Guest::pluck('name', 'id')->toArray())
                            ->placeholder('Select an existing guest or create a new one')
                            ->reactive()  // Allows for dynamic updates when a guest is selected
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                // Find the selected guest
                                $guest = Guest::find($state);

                                if ($guest && $guest->stay_count >= 5) {
                                    // Set a placeholder or reminder message for frequent guests
                                    $set('frequent_guest_message', "{$guest->name}  has stayed {$guest->stay_count} times and is eligible for a discount.");
                                } else {
                                    // Clear the message if guest does not qualify
                                    $set('frequent_guest_message', null);
                                }
                            })
                            ->createOptionForm([
                                TextInput::make('name')->label('Full Name')->required()->maxLength(255),
                                TextInput::make('phone_number')->label('Phone Number')->unique(Guest::class, 'phone_number')->maxLength(255),
                                TextInput::make('nin_number')->label('NIN Number')->unique(Guest::class, 'nin_number')->maxLength(255),
                                Textarea::make('preferences')->label('Preferences')->placeholder('E.g., Halal food, quiet room'),
                            ])
                            ->createOptionAction(function (Action $action) {
                                return $action->modalHeading('Create New Guest')->modalButton('Add Guest')->modalWidth('lg');
                            })
                            ->createOptionUsing(function ($data) {
                                return Guest::create($data)->id;
                            }),

                        // Show a message if the guest is frequent
                        Placeholder::make('frequent_guest_message')
                            ->label('Frequent Guest')
                            ->content(fn($get) => $get('frequent_guest_message'))
                            ->visible(fn($get) => !is_null($get('frequent_guest_message'))),  // Only visible if there's a message
                    ]),

                // Step 2: Reservation Details
                Step::make('Reservation Details')
                    ->icon('heroicon-o-calendar')
                    ->description('Provide reservation details including room and stay duration.')
                    ->completedIcon('heroicon-m-check-circle')
                    ->columns(2)
                    ->schema([
                        Select::make('room_id')
                            ->label('Select Room')
                            ->searchable()
                            ->options(Room::where('status', 1)->pluck('room_number', 'id')->toArray()) // Only available rooms
                            ->placeholder('Choose a room')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $room = Room::find($state);
                                if ($room) {
                                    $set('price_per_night', $room->price_per_night ?? 0);
                                    static::updateTotalAmount($get, $set);
                                }
                            }),
                        TextInput::make('price_per_night')
                            ->label('Price per Night')
                            ->readOnly(),


                        DatePicker::make('check_in_date')
                            ->label('Check-In Date')
                            ->required()
                            ->reactive()
                            ->closeOnDateSelection()
                            // ->native(false)
                            ->default(now())
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                static::updateTotalAmount($get, $set);
                                static::updateNumberOfNights($get, $set);
                            }),

                        DatePicker::make('check_out_date')
                            ->label('Check-Out Date')
                            ->closeOnDateSelection()
                            // ->native(false)
                            ->required()
                            ->afterOrEqual('check_in_date')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                static::updateTotalAmount($get, $set);
                                static::updateNumberOfNights($get, $set);
                            }),

                        TextInput::make('number_of_people')->label('Number of People')->required(),
                        TextInput::make('number_of_nights')->readOnly()->label('Number of Nights')
                    ]),

                // Step 3: Apply Discounts
                Step::make('Apply Discounts')
                    ->icon('heroicon-o-tag')
                    ->description('Apply any available discount coupons.')
                    ->completedIcon('heroicon-m-check-circle')
                    ->schema([
                        Select::make('coupon_id')->label('Apply Coupon')->searchable()->options(Coupon::where('status', 'active')->pluck('code', 'id')->toArray())->reactive()->afterStateUpdated(function ($state, callable $get, callable $set) {
                            $coupon = Coupon::find($state);
                            if ($coupon) {
                                static::applyCoupon($coupon, $get, $set);
                            }
                        }),
                        TextInput::make('discount_amount')->label('Discount Amount')->readOnly(),
                        TextInput::make('total_amount')->label('Total Amount')->readOnly(),
                    ]),

                // Step 4: Payment Details
                Step::make('Payment Details')
                    ->icon('heroicon-o-credit-card')
                    ->description('Enter payment details and check payment status.')
                    ->completedIcon('heroicon-m-check-circle')
                    ->columns(2)
                    ->schema([
                        Select::make('payment_method')
                            ->label('Payment Method')
                            ->searchable()
                            ->options([
                                'Card' => 'Card Payment',
                                'Transfer' => 'Transfer Payment',
                                'Cash' => 'Cash Payment',
                            ]),
                        TextInput::make('amount_paid')->label('Amount Paid')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                static::checkPaymentStatus($get, $set);
                            }),
                        TextInput::make('remaining_balance')
                            ->label('Remaining Balance')
                            ->readOnly(),
                        TextInput::make('payment_status')
                            ->label('Payment Status')->readOnly(),
                    ]),

                // Step 5: Special Requests & Final Confirmation
                Step::make('Special Requests & Confirmation')
                    ->icon('heroicon-o-check')
                    ->description('Add any special requests and confirm the reservation.')
                    ->completedIcon('heroicon-m-check-circle')
                    ->schema([
                        Textarea::make('special_requests')
                            ->label('Special Requests (Optional)'),
                        Select::make('status')
                            ->searchable()

                            ->label('Reservation Status')->options([
                                    'Confirmed' => 'Confirmed',
                                    'On Hold' => 'On Hold',
                                ])->required(),
                    ]),
            ])->skippable()
                ->columnSpanFull()
        ]);
    }

    protected static function updateTotalAmount(callable $get, callable $set)
    {
        $checkInDate = Carbon::parse($get('check_in_date'));
        $checkOutDate = Carbon::parse($get('check_out_date'));

        if ($checkInDate && $checkOutDate) {
            $days = $checkInDate->diffInDays($checkOutDate);
            $pricePerNight = $get('price_per_night');
            $totalAmount = $days * $pricePerNight;

            // Apply coupon if available

            $discount = $get('discount_amount') ?? 0;
            $total = max(0, $totalAmount - $discount);

            $set('total_amount', $total);
        } else {
            $set('total_amount', 0);
        }
    }
    public static function updateNumberOfNights(callable $get, callable $set)
    {
        $checkInDate = $get('check_in_date');
        $checkOutDate = $get('check_out_date');

        if ($checkInDate && $checkOutDate) {
            $checkIn = \Carbon\Carbon::parse($checkInDate);
            $checkOut = \Carbon\Carbon::parse($checkOutDate);

            $numberOfNights = $checkIn->diffInDays($checkOut);
            $set('number_of_nights', $numberOfNights);
        } else {
            $set('number_of_nights', 0);
        }
    }

    public static function applyCoupon($coupon, callable $get, callable $set)
    {
        $totalAmount = $get('total_amount');
        $discountAmount = 0;

        // Validate coupon application
        if ($coupon->discount_type === 'percentage') {
            $discountAmount = ($totalAmount * $coupon->discount_percentage) / 100;
        } elseif ($coupon->discount_type === 'fixed') {
            $discountAmount = min($totalAmount, $coupon->discount_amount);  // Prevent discount from exceeding total
        }

        $set('discount_amount', $discountAmount);
        static::updateTotalAmount($get, $set);
    }

    public static function checkPaymentStatus(callable $get, callable $set)
    {
        $totalAmount = (float) preg_replace('/[^0-9.]/', '', $get('total_amount', 0));
        $amountPaid = (float) preg_replace('/[^0-9.]/', '', $get('amount_paid', 0));

        // Validate payment to prevent overpaying
        if ($amountPaid > $totalAmount) {
            $set('payment_status', 'Overpayment detected');
            $amountPaid = $totalAmount;
        }

        $remainingBalance = $totalAmount - $amountPaid;
        $set('remaining_balance', $remainingBalance);

        // Mark the payment as partial or full
        if ($remainingBalance > 0) {
            $set('payment_status', 'Partial Payment');
        } else {
            $set('payment_status', 'Full Payment');
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reservation_number')
                    ->label('Reservation No.')
                    ->sortable()
                    ->color(fn(?Model $record): array => \Filament\Support\Colors\Color::hex(optional($record->tenant)->color ?? '#15803d'))
                    ->weight('bold')
                    ->searchable(),
                TextColumn::make('guest.name')
                    ->label('Guest Name')
                    ->sortable()
                    ->color(fn(?Model $record): array => \Filament\Support\Colors\Color::hex(optional($record->tenant)->color ?? '#22e03a'))
                    ->weight('bold')
                    ->toggleable()
                    ->searchable(),

                TextColumn::make('room.room_number')
                    ->label('Room Number')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('check_in_date')
                    ->label('Check-In Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('check_out_date')
                    ->label('Check-Out Date')
                    ->date()
                    ->sortable(),
                BadgeColumn::make('status')
                    ->label('Status')
                    ->color(fn(string $state): string => match ($state) {
                        'Confirmed' => 'info',
                        'On Hold' => 'danger',
                        'Checked In' => 'success',
                        'Checked Out' => 'warning',
                    })
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->label('Total Amount')
                    ->money('NGN', true)
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-o-pencil'),
                Tables\Actions\Action::make('checkIn')
                    ->label('Check In')
                    ->icon('heroicon-o-arrow-left-end-on-rectangle')
                    ->color('success') // Optional: Changes button color
                    ->action(function (Reservation $record) {
                        // Create the new CheckIn record with reservation data
                        CheckIn::create([
                            'user_id' => $record->user_id,
                            'reservation_number' => $record->reservation_number,
                            'check_in_time' => now(),
                            'guest_name' => $record->guest->name,
                            'guest_phone' => $record->guest->phone_number,
                            'paid_amount' => $record->amount_paid,
                            'room_number' => $record->room->room_number,
                            'due_amount' => $record->remaining_balance,
                            'booking_status' => 'Checked In',
                            'payment_status' => $record->payment_status,
                            'coupon_management' => $record->coupon_id,
                            'coupon_discount' => $record->discount_amount,
                            'price_per_night' => $record->price_per_night,
                            'frequent_guest_message' => $record->frequent_guest_message,
                            'number_of_nights' => $record->number_of_nights,
                            'special_requests' => $record->special_requests,
                            'number_of_people' => $record->number_of_people,
                            'total_amount' => $record->total_amount,
                        ]);


                        // Delete the reservation after check-in
                        $record->delete();

                        // Optional: Notify the user
                        Notification::make()
                            ->title('Success')
                            ->body('Check-in successful and reservation removed!')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation() // Adds confirmation dialog before proceeding
                    ->modalHeading('Confirm Check In') // Optional: Custom modal heading
                    ->modalButton('Check In') // Optional: Custom button label on confirmation modal
                    ->modalSubheading('Are you sure you want to check in this reservation?')
            ])

            ->defaultSort('created_at', 'desc');

    }

    public static function getEmailWizard($record): array
    {
        return [
            Step::make('Reservation Details')
                ->schema([
                    TextInput::make('guest_name')
                        ->default($record->guest->name)
                        ->disabled()
                        ->label('Guest Name'),

                    TextInput::make('room_number')
                        ->default($record->room->room_number)
                        ->disabled()
                        ->label('Room Number'),

                    TextInput::make('check_in_date')
                        ->default($record->check_in_date)
                        ->disabled()
                        ->label('Check-in Date'),

                    TextInput::make('check_out_date')
                        ->default($record->check_out_date)
                        ->disabled()
                        ->label('Check-out Date'),

                    TextInput::make('total_amount')
                        ->default($record->total_amount)
                        ->disabled()
                        ->label('Total Amount'),
                ]),

            Step::make('Email Message')
                ->schema([
                    RichEditor::make('email_content')
                        ->label('Custom Message')
                        ->placeholder('Write a custom message for the guest...')
                ]),

            Step::make('Confirmation')
                ->schema([
                    Checkbox::make('confirm_send')
                        ->label('Confirm sending this email?')
                        ->required(),
                ]),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Guest Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('guest.name')
                                    ->label('Guest Name'),
                                TextEntry::make('guest.phone_number')
                                    ->label('Phone Number'),
                                TextEntry::make('guest.nin_number')
                                    ->label('NIN Number'),
                                TextEntry::make('guest.stay_count')
                                    ->label('Stay Count'),
                            ]),
                    ]),

                Section::make('Reservation Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('room.room_number')
                                    ->label('Room Number'),
                                TextEntry::make('price_per_night')
                                    ->label('Price Per Night'),
                                TextEntry::make('check_in_date')
                                    ->label('Check-In Date'),
                                TextEntry::make('check_out_date')
                                    ->label('Check-Out Date'),
                                TextEntry::make('number_of_nights')
                                    ->label('Number of Nights'),
                                TextEntry::make('number_of_people')
                                    ->label('Number of People'),
                            ]),
                    ]),

                Section::make('Discount Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('coupon.code')
                                    ->label('Coupon Code'),
                                TextEntry::make('discount_amount')
                                    ->label('Discount Amount'),
                                TextEntry::make('total_amount')
                                    ->label('Total Amount'),
                            ]),
                    ]),

                Section::make('Payment Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('payment_method')
                                    ->label('Payment Method'),
                                TextEntry::make('amount_paid')
                                    ->label('Amount Paid'),
                                TextEntry::make('remaining_balance')
                                    ->label('Remaining Balance'),
                                TextEntry::make('payment_status')
                                    ->label('Payment Status'),
                            ]),
                    ]),

                Section::make('Special Requests & Confirmation')
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                TextEntry::make('special_requests')
                                    ->label('Special Requests'),
                                TextEntry::make('status')
                                    ->label('Reservation Status'),
                            ]),
                    ]),
            ]);
    }

    // public static function scheduleExpiration($reservationId)
    // {
    //     ExpireReservation::dispatch($reservationId)->delay(now()->addHours(1));
    // }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReservations::route('/'),
            'create' => Pages\CreateReservation::route('/create'),
            'view' => Pages\ViewReservation::route('/{record}'),
            'edit' => Pages\EditReservation::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Filament::getCurrentPanel()?->getId() === 'frontdesk';
    }
}
