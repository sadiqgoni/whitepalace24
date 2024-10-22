<?php

namespace App\Filament\Frontdesk\Resources;

use App\Filament\Frontdesk\Resources\GuestResource\Pages;
use App\Filament\Frontdesk\Resources\GuestResource\RelationManagers;
use App\Models\Guest;
use Filament\Facades\Filament;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Card;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Model;


class GuestResource extends Resource
{
    protected static ?string $model = Guest::class;

    protected static ?string $navigationGroup = 'Guest Management';
    protected static ?string $navigationLabel = 'Guest Records';
    protected static ?string $modelLabel = 'Guest Records';
    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
            // Helper function to format names
            $formatName = function ($state, callable $set, $field) {
                $formatted = ucwords(strtolower($state));
                $set($field, $formatted);
            };
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Section::make('Personal Information')
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->live(onBlur:true)
                                    ->label('Full Name')
                                    ->afterStateUpdated(fn($state, $set) => $formatName($state, $set, 'name'))
                                    ->placeholder('Enter first name')
                                    ->maxLength(255),
                                TextInput::make('phone_number')
                                    ->nullable()
                                    ->label('Phone Number')
                                    ->placeholder('Enter phone number')
                                    ->unique(Guest::class, 'phone_number')
                                    ->maxLength(255),
                                TextInput::make('nin_number')
                                    ->label('NIN Number')
                                    ->placeholder('Enter NIN')
                                    ->maxLength(255)
                                    ->unique(Guest::class, 'nin_number'),
                                Textarea::make('preferences')
                                    ->label('Preferences')
                                    ->afterStateUpdated(fn($state, $set) => $formatName($state, $set, 'preferences'))

                                    ->placeholder('Enter Preferences'),
                            ])
                            ->columns(2),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Full Name')
                    ->sortable()
                    ->color(fn(?Model $record): array => \Filament\Support\Colors\Color::hex(optional($record->tenant)->color ?? '#22e03a'))
                    ->weight('bold')
                    ->searchable(),
                TextColumn::make('phone_number')
                    ->label('Phone Number')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('nin_number')
                    ->label('NIN Number')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('preferences')
                    ->label('Preferences')
                    ->sortable()
                    ->searchable(),
                BadgeColumn::make('stay_count')
                    ->label('Total Stay')
                    ->sortable()
                    ->colors([
                        'success' => fn($state) => $state >= 5,
                    ])
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    // ->toggleable(isToggledHiddenByDefault: true),

            ])->defaultSort('created_at', 'desc')

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
                ])

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
            'index' => Pages\ListGuests::route('/'),
            'create' => Pages\CreateGuest::route('/create'),
            'view' => Pages\ViewGuest::route('/{record}'),
            'edit' => Pages\EditGuest::route('/{record}/edit'),
        ];
    }
    public static function shouldRegisterNavigation(): bool
    {
        return Filament::getCurrentPanel()?->getId() === 'frontdesk';
    }
}
