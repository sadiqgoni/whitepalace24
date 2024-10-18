<?php

namespace App\Filament\Management\Resources;

use App\Filament\Management\Resources\CouponResource\Pages;
use App\Filament\Management\Resources\CouponResource\RelationManagers;
use App\Models\Coupon;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;


class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Sales Promo';
    public static function form(Form $form): Form
    {
        return $form
            ->schema(components: [

                Forms\Components\Card::make()

                    ->schema(components: [
                        // Coupon Details Section
                        Forms\Components\Card::make()
                            ->schema([
                                TextInput::make('code')
                                    ->label('Coupon Code')
                                    ->required()
                                    ->unique(ignorable: fn($record) => $record),

                                Textarea::make('description')
                                    ->label('Description')
                                    ->placeholder('Brief description of the coupon')
                                    ->required(),
                            ])
                            ->columnSpan(2),

                        // Discount Details Section
                        Forms\Components\Card::make()
                            ->schema([
                                Select::make('discount_type')
                                    ->label('Discount Type')
                                    ->options([
                                        'percentage' => 'Percentage',
                                        'fixed' => 'Fixed Amount',
                                    ])
                                    ->required()
                                    ->reactive(),

                                TextInput::make('discount_percentage')
                                    ->label('Discount Percentage (%)')
                                    ->numeric()
                                    ->placeholder('Enter percentage')
                                    ->visible(fn($get) => $get('discount_type') === 'percentage') // Show only if type is percentage
                                    ->required(fn($get) => $get('discount_type') === 'percentage'),

                                TextInput::make('discount_amount')
                                    ->label('Discount Amount (₦)')
                                    ->numeric()
                                    ->placeholder('Enter discount amount')
                                    ->visible(fn($get) => $get('discount_type') === 'fixed') // Show only if type is fixed
                                    ->required(fn($get) => $get('discount_type') === 'fixed'),
                            ])
                            ->columns(2),

                        Forms\Components\Card::make()
                            ->schema([
                                DatePicker::make('valid_from')
                                    ->label('Valid From')
                                    ->required(),

                                DatePicker::make('valid_until')
                                    ->label('Valid Until')
                                    ->required(),
                            ])
                            ->columns(2),

                        // Usage Limit Section
                        Forms\Components\Card::make()
                            ->schema([
                                TextInput::make('usage_limit')
                                    ->label('Usage Limit')
                                    ->numeric()
                                    ->placeholder('Max uses allowed')
                                    ->required(),

                                TextInput::make('times_used')
                                    ->label('Times Used')
                                    ->numeric()
                                    ->default(0)
                                    ->disabled(),

                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'active' => 'Active',
                                        'inactive' => 'Inactive',
                                    ])
                                    ->default('active')
                                    ->required(),
                            ])
                            ->columns(2),
                    ])
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Coupon Code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Description')
                    ->limit(50),

                BadgeColumn::make('discount_type')
                    ->label('Type')
                    ->colors([
                        'primary' => 'percentage',
                        'success' => 'fixed',
                    ]),

                // Conditional column display based on discount type
                TextColumn::make('discount_value')
                    ->label('Discount')
                    ->getStateUsing(function ($record) {
                        return $record->discount_type === 'percentage'
                            ? $record->discount_percentage . '%'
                            : '₦' . number_format($record->discount_amount, 2);
                    })
                    ->sortable(),

                TextColumn::make('valid_from')
                    ->label('Valid From')
                    ->date()
                    ->sortable(),

                TextColumn::make('valid_until')
                    ->label('Valid Until')
                    ->date()
                    ->sortable(),

                TextColumn::make('usage_limit')
                    ->label('Usage Limit')
                    ->sortable(),

                TextColumn::make('times_used')
                    ->label('Times Used')
                    ->sortable(),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                    ]),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ]),

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc')

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }


    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([

                // Coupon Details Section
                \Filament\Infolists\Components\Section::make('Coupon Details')
                    ->schema([
                        Split::make([
                            Grid::make(2)
                                ->schema([
                                    TextEntry::make('code')
                                        ->label('Coupon Code'),

                                    TextEntry::make('description')
                                        ->label('Description'),
                                ]),
                        ]),
                    ]),

                // Discount Details Section
                \Filament\Infolists\Components\Section::make('Discount Details')
                    ->schema([
                        Split::make([
                            Grid::make(2)
                                ->schema([
                                    TextEntry::make('discount_type')
                                        ->label('Discount Type'),

                                    TextEntry::make('discount_percentage')
                                        ->label('Discount Percentage (%)')
                                        ->visible(fn($record) => $record->discount_type === 'percentage'),

                                    TextEntry::make('discount_amount')
                                        ->label('Discount Amount (₦)')
                                        ->visible(fn($record) => $record->discount_type === 'fixed'),
                                ]),
                        ]),
                    ]),

                // Validity Period Section
                \Filament\Infolists\Components\Section::make('Validity Period')
                    ->schema([
                        Split::make([
                            Grid::make(2)
                                ->schema([
                                    TextEntry::make('valid_from')
                                        ->label('Valid From')
                                        ->date(),

                                    TextEntry::make('valid_until')
                                        ->label('Valid Until')
                                        ->date(),
                                ]),
                        ]),
                    ]),

                // Usage Limit Section
                \Filament\Infolists\Components\Section::make('Usage Limit')
                    ->schema([
                        Split::make([
                            Grid::make(2)
                                ->schema([
                                    TextEntry::make('usage_limit')
                                        ->label('Usage Limit'),

                                    TextEntry::make('times_used')
                                        ->label('Times Used'),

                                    TextEntry::make('status')
                                        ->badge()
                                        ->colors([
                                            'success' => 'active',
                                            'danger' => 'inactive',
                    
                                        ])
                                        ->label('Status'),
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
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'view' => Pages\ViewCoupon::route('/{record}'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
    public static function shouldRegisterNavigation(): bool
    {
        return Filament::getCurrentPanel()?->getId() === 'management';
    }
}
