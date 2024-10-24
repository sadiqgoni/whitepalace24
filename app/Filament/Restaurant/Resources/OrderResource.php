<?php

namespace App\Filament\Restaurant\Resources;

use App\Filament\Restaurant\Resources\OrderResource\Pages;
use App\Filament\Restaurant\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use App\Models\User;
use Filament\Tables\Table;


class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Sales';
    protected static ?string $label = 'Order';
    protected static ?string $pluralLabel = 'Orders';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('Invoice No.')
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer_type')
                    ->label('Customer Name')
                    ->searchable()
                    ->formatStateUsing(function ($record) {
                        if ($record->customer_type === 'walkin') {
                            $previousWalkinCount = $record::where('customer_type', 'walkin')
                                ->where('id', '<=', $record->id)
                                ->count();
                            return 'Customer 00' . str_pad($previousWalkinCount, 3, '0', STR_PAD_LEFT);
                        } elseif ($record->customer_type === 'guest') {
                            return $record->guest_info;
                        }
                        return 'Unknown Customer';
                    }),
                Tables\Columns\TextColumn::make('dining_option')
                    ->label('Dining Option')
                    ->formatStateUsing(function ($record) {
                        return $record->dining_option === 'takeout'
                            ? 'Takeout'
                            : ($record->table ? $record->table->name : 'Dine In');
                    }),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total Amount')
                    ->money('NGN'),
                Tables\Columns\BadgeColumn::make('payment_method')
                    ->label('Payment Method')
                    ->searchable()
                    ->colors([
                        'success' => 'transfer',
                        'warning' => 'card',
                        'info' => 'cash'
                    ]),
                Tables\Columns\TextColumn::make('user_id')
                    ->label('Cashier')
                    ->formatStateUsing(fn($state) => User::find($state)?->name ?? 'Deleted User'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->date(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('customer_type')
                    ->options([
                        'walkin' => 'Walk-in',
                        'guest' => 'Guest',
                    ])
                    ->placeholder('Select Customer Type'),

                Tables\Filters\SelectFilter::make('payment_method')
                    ->options([
                        'cash' => 'Cash',
                        'card' => 'Card',
                        'transfer' => 'Transfer',
                    ])
                    ->placeholder('Payment Method'),
            ])
            ->actions([

                Tables\Actions\Action::make('openPaymentModal')
                    ->label('Make Payment')
                    ->icon('heroicon-o-banknotes')
                    ->modalContent(fn(Order $record) => view('filament.pages.order-table', ['order' => $record]))
                    ->color('success')
                    ->modalWidth(\Filament\Support\Enums\MaxWidth::Medium)
                    ->modalSubmitActionLabel('Close')
                    ->visible(fn(Order $record) => $record->billing_option !== 'charge_room'),
                Tables\Actions\Action::make('generateInvoice')
                    ->label('Generate Invoice')
                    ->icon('heroicon-o-document-text')
                    ->url(fn(Order $record) => route('invoice.generate', $record))
                    ->openUrlInNewTab()
                    ->color('primary'),

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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
