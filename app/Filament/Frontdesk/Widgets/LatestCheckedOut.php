<?php
namespace App\Filament\Frontdesk\Widgets;

use App\Models\CheckInCheckOut;
use App\Models\CheckOut;
use Carbon\Carbon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Model;

class LatestCheckedOut extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';
    protected static ?string $heading = 'Checked Out Guests';


    public function table(Table $table): Table
    {
        return $table
            ->query(CheckOut::query()->latest())

            ->columns([
                Split::make([
                    Stack::make([
                        TextColumn::make('guest_name')
                            ->description('Guest Name', 'above')
                            ->sortable()      
                            ->alignLeft()
                            ->color(fn(?Model $record): array => \Filament\Support\Colors\Color::hex(optional($record->tenant)->color ?? '#15803d'))
                            ->weight('bold'),
                   
                    ])->space(),

                    Stack::make([
                        TextColumn::make('room_number')
                            ->description('Room Number', 'above')
                            ->sortable()
                            ->alignLeft(),
                    ])->space(),
                    Stack::make([

                        TextColumn::make('check_in_time')
                            ->description('Check-In Time', 'above')
                            ->sortable()
                            ->alignLeft(),

                    ])->space(),
                    Stack::make([

                        TextColumn::make('check_out_time')
                            ->description('Check-Out Time', 'above')
                            ->sortable()
                            ->alignLeft(),

                    ])->space(),
                    Stack::make([

                    
                    ])->space(),
                ])->from('md'),
            ])
            ->filters([
                //
            ])
            ->actions([
            ])
            ->groupedBulkActions([
        
            ]);
    }
}
