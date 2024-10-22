<?php

namespace App\Filament\Frontdesk\Resources;

use App\Filament\Frontdesk\Resources\CarResource\Pages;
use App\Filament\Frontdesk\Resources\CarResource\RelationManagers;
use App\Models\Car;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Card;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;



class CarResource extends Resource
{
    protected static ?string $model = Car::class;
    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = 'Transport Management';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Section::make('')
                            ->schema([
                                TextInput::make('car_name')
                                    ->label('Car Name')
                                    ->required(),
                                TextInput::make('car_type')
                                    ->label('Car Type')
                                    ->required(),
                                TextInput::make('number_plate')
                                    ->label('Number Plate')
                                    ->required(),
                                Select::make('availability_status')
                                    ->label('Availability')
                                    ->options([
                                        'available' => 'Available',
                                        'rented' => 'Rented',
                                        'maintenance' => 'Maintenance',
                                    ]),
                                TextInput::make('rate_per_hour')
                                    ->label('Rate Per Hour')
                                    ->numeric()
                                    ->required(),
                            ])
                            ->columns(2),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('car_name')->label('Car Name')
                ->color(fn(?Model $record): array => \Filament\Support\Colors\Color::hex(optional($record->tenant)->color ?? '#22e03a'))
                ->weight('bold'),
                TextColumn::make('car_type')->label('Car Type')
                ->color(fn(?Model $record): array => \Filament\Support\Colors\Color::hex(optional($record->tenant)->color ?? '#15803d'))
                ->weight('bold'),
                TextColumn::make('number_plate')->label('Number Plate'),
                TextColumn::make('availability_status')->label('Status')->sortable(),
                TextColumn::make('rate_per_hour')->label('Rate Per Hour')->money('NGN', true)

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCars::route('/'),
            'create' => Pages\CreateCar::route('/create'),
            'view' => Pages\ViewCar::route('/{record}'),
            'edit' => Pages\EditCar::route('/{record}/edit'),
        ];
    }
    public static function shouldRegisterNavigation(): bool
    {
        return Filament::getCurrentPanel()?->getId() === 'frontdesk';
    }
}
