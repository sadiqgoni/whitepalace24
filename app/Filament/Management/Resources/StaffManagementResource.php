<?php

namespace App\Filament\Management\Resources;

use App\Filament\Management\Resources\StaffManagementResource\Pages;
use App\Filament\Management\Resources\StaffManagementResource\RelationManagers;
use App\Models\StaffManagement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\ImageEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
class StaffManagementResource extends Resource
{
    protected static ?string $model = StaffManagement::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Management';
    public static function form(Form $form): Form
    {
        // Helper function to format names
        $formatName = function ($state, callable $set, $field) {
            $formatted = ucwords(strtolower($state));
            $set($field, $formatted);
        };

        return $form->schema([
            Card::make()->schema([
                // Personal Information Section
                Forms\Components\Section::make('Personal Information')->schema([
                    Forms\Components\Grid::make(2)->schema([

                        Forms\Components\TextInput::make('full_name')
                            ->required()
                            ->label('Full Name')
                            ->placeholder('Enter full name')
                            ->afterStateUpdated(fn($state, $set) => $formatName($state, $set, 'full_name'))
                            ->live(onBlur: true)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->required()
                            ->label('Email Address')
                            ->email()
                            ->unique(StaffManagement::class, 'email', ignoreRecord: true)
                            ->placeholder('Enter email address'),

                        Forms\Components\TextInput::make('phone_number')
                            ->label('Phone Number')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('Enter contact number'),

                        DatePicker::make('date_of_birth')
                            ->label('Date of Birth'),

                        Forms\Components\FileUpload::make('profile_picture')
                            ->label('Profile Picture')
                            ->disk('public') // Can move to config('filesystems.disks.profile')
                            ->image()
                            ->directory('profile-pictures') // Better placed in config
                            ->placeholder('Upload a profile picture'),
                    ]),
                ]),

                // Employment Details Section
                Forms\Components\Section::make('Employment Details')->schema([
                    Forms\Components\Grid::make(2)->schema([

                        Forms\Components\Select::make('role')
                            ->required()
                            ->label('Role')
                            ->options([
                                'Manager' => 'Manager',
                                'Receptionist' => 'Receptionist',
                                'Housekeeper' => 'Housekeeper',
                                'Security' => 'Security',
                                'Maintenance' => 'Maintenance',
                            ]) // Could be loaded from a database or enum
                            ->searchable()
                            ->placeholder('Select role'),

                        Forms\Components\DatePicker::make('employment_date')
                            ->label('Employment Date')
                            ->required()
                            ->placeholder('Select employment date'),

                        Forms\Components\DatePicker::make('termination_date')
                            ->label('Termination Date')
                            ->placeholder('Select termination date'),

                        Forms\Components\Select::make('shift')
                            ->label('Shift')
                            ->options([
                                'Morning' => 'Morning',
                                'Evening' => 'Evening',
                                'Night' => 'Night',
                            ]) // Same as 'role', could be database driven
                            ->searchable()
                            ->placeholder('Select shift'),
                    ]),
                ]),

                // Additional Information Section
                Forms\Components\Section::make('Additional Information')->schema([
                    Forms\Components\Grid::make(2)->schema([

                        Forms\Components\Textarea::make('address')
                            ->label('Address')
                            ->afterStateUpdated(fn($state, $set) => $formatName($state, $set, 'address'))
                            ->live(onBlur: true)
                            ->placeholder('Enter address'),

                        Forms\Components\Select::make('status')
                            ->label('Employment Status')
                            ->options([
                                'active' => 'Active',
                                'suspended' => 'Suspended',
                                'terminated' => 'Terminated',
                            ])
                            ->searchable()
                            ->default('active')
                            ->required(),
                    ]),
                ]),

                // Next of Kin Information Section
                Forms\Components\Section::make('Next of Kin Information')->schema([
                    Forms\Components\Grid::make(2)->schema([

                        Forms\Components\TextInput::make('next_of_kin_name')
                            ->label('Next of Kin Name')
                            ->afterStateUpdated(fn($state, $set) => $formatName($state, $set, 'next_of_kin_name'))
                            ->live(onBlur: true)
                            ->placeholder('Enter next of kin name')
                            ->maxLength(255)
                            ->required(),

                        Forms\Components\Textarea::make('next_of_kin_address')
                            ->label('Next of Kin Address')
                            ->afterStateUpdated(fn($state, $set) => $formatName($state, $set, 'next_of_kin_address'))
                            ->live(onBlur: true)
                            ->placeholder('Enter next of kin address')
                            ->maxLength(500)
                            ->required(),

                        Forms\Components\TextInput::make('next_of_kin_phone_number')
                            ->label('Next of Kin Phone Number')
                            ->tel()
                            ->placeholder('Enter next of kin phone number')
                            ->maxLength(20)
                            ->required(),
                    ]),
                ]),
            ]),
        ]);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->sortable(),
                ImageColumn::make('profile_picture')
                    ->label('Profile Picture')
                    ->rounded() 
                    ->width(50) 
                    ->height(50),
                TextColumn::make('full_name')
                    ->label('Full Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                BadgeColumn::make('role')
                    ->label('Role')
                    ->colors([
                        'primary' => 'Manager',
                        'success' => 'Receptionist',
                        'warning' => 'Housekeeper',
                        'info' => 'Security',
                        'danger' => 'Maintenance',
                    ]),

                TextColumn::make('phone_number')
                    ->label('Phone')
                    ->sortable(),

                 BadgeColumn::make('shift')
                    ->label('Shift')
                    ->colors([
                        'success' => 'Morning',
                        'warning' => 'Evening',
                        'grey' => 'Night',
                    ]),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'suspended',
                        'warning' => 'terminated',
                    ]),

                TextColumn::make('employment_date')
                    ->label('Employment Date')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Filter by Status')
                    ->options([
                        'active' => 'Active',
                        'suspended' => 'Suspended',
                        'terminated' => 'Terminated',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
    
                // Personal Information Section
                \Filament\Infolists\Components\Section::make('Personal Information')
                    ->schema([
                        Split::make([
                            Grid::make(2)
                                ->schema([
                                    TextEntry::make('full_name')
                                        ->label('Full Name'),
    
                                    TextEntry::make('email')
                                        ->label('Email Address'),
    
                                    TextEntry::make('phone_number')
                                        ->label('Phone Number'),
    
                                    TextEntry::make('date_of_birth')
                                        ->label('Date of Birth')
                                        ->date(),
                                ]),
                        ]),
                    ]),
    
                // Employment Details Section
                \Filament\Infolists\Components\Section::make('Employment Details')
                    ->schema([
                        Split::make([
                            Grid::make(2)
                                ->schema([
                                    TextEntry::make('role')
                                        ->label('Role'),
    
                                    TextEntry::make('employment_date')
                                        ->label('Employment Date')
                                        ->date(),
    
                                    TextEntry::make('termination_date')
                                        ->label('Termination Date')
                                        ->date()
                                        ->hidden(fn($record) => $record->termination_date === null),
    
                                    TextEntry::make('shift')
                                        ->label('Shift'),
    
                                    TextEntry::make('status')
                                        ->label('Employment Status'),
                                ]),
                        ]),
                    ]),
    
                // Next of Kin Information Section
                \Filament\Infolists\Components\Section::make('Next of Kin Information')
                    ->schema([
                        Split::make([
                            Grid::make(2)
                                ->schema([
                                    TextEntry::make('next_of_kin_name')
                                        ->label('Next of Kin Name'),
    
                                    TextEntry::make('next_of_kin_phone_number')
                                        ->label('Next of Kin Phone Number'),
    
                                    TextEntry::make('next_of_kin_address')
                                        ->label('Next of Kin Address'),
                                ]),
                        ]),
                    ]),
    
                // Additional Information Section
                \Filament\Infolists\Components\Section::make('Additional Information')
                    ->schema([
                        Split::make([
                            Grid::make(2)
                                ->schema([
                                    TextEntry::make('address')
                                        ->label('Address'),
    
                                        ImageEntry::make('profile_picture')
                                        ->label('Profile Picture')
                                ]),
                        ]),
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
            'index' => Pages\ListStaffManagement::route('/'),
            'create' => Pages\CreateStaffManagement::route('/create'),
            'view' => Pages\ViewStaffManagement::route('/{record}'),
            'edit' => Pages\EditStaffManagement::route('/{record}/edit'),
        ];
    }
}
