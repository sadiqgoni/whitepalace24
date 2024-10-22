<?php 
namespace App\Filament\Frontdesk\Widgets;

use App\Models\CheckIn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestCheckedIn extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Checked In Guests';


    public function table(Table $table): Table
    {
        return $table
        ->query(CheckIn::query()->latest()) // Query records where status is "Checked In"

            ->columns([
               Split::make([
                    Stack::make([
                        TextColumn::make('guest_name')
                        ->description('Guest Name','above')
                            ->sortable()
                            ->weight('bold')
                            ->alignLeft(),

                            TextColumn::make('guest_phone')
                            ->searchable()
                            ->sortable()
                            ->color('gray')
                            ->alignLeft(),
                    ])->space(),

                   Stack::make([
                            TextColumn::make('room_number')
                            ->description('Room Number','above')
                            ->sortable()
                            ->alignLeft(),
                    ])->space(),
                    Stack::make([
                
                        TextColumn::make('check_in_time')
                        ->description('Check-In Time','above')
                        ->sortable()
                        ->alignLeft(),
             
                ])->space(),
                Stack::make([

                    TextColumn::make('check_out_time')
                    ->description('Check-Out Time','above')
                    ->sortable()
                    ->alignLeft(),
         
            ])->space(),
         
                ])->from('md'),
            ])
            ->filters([
                //
            ])
          
            ->groupedBulkActions([
             
            ]);
    }
}
