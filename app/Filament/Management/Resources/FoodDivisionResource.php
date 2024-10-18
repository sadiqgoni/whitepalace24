<?php

namespace App\Filament\Management\Resources;

use App\Filament\Management\Resources\FoodDivisionResource\Pages;
use App\Filament\Management\Resources\FoodDivisionResource\RelationManagers;
use App\Models\FoodDivision;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FoodDivisionResource extends Resource
{
    protected static ?string $model = FoodDivision::class;

    protected static ?string $navigationIcon = 'heroicon-o-view-columns';
    protected static ?string $navigationGroup = 'Food Section';
    public static function form(Form $form): Form
    {
        // Helper function to format names
        $formatName = function ($state, callable $set, $field) {
            $formatted = ucwords(strtolower($state));
            $set($field, $formatted);
        };
        return $form
            ->schema([
                Section::make('')
                    ->schema([
                        TextInput::make('name')
                            ->label('Classification Name')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn($state, $set) => $formatName($state, $set, 'name'))
                            ->maxLength(255),
                        // Icon selection dropdown
                        Select::make('icon')
                            ->options([
                                '🍲' => '🍲',
                                '🍜' => '🍜',
                                '🥣' => '🥣',
                                '🍽️' => '🍽️',
                                '🥗' => '🥗',
                                '☕' => '☕',
                                '🍔' => '🍔',
                                '🍗' => '🍗',
                                '🍟' => '🍟',
                                '🍕' => '🍕',
                                '🍳' => '🍳',
                                '🥤' => '🥤',
                                '🍰' => '🍰',
                            ])
                            ->required()
                            ->helperText('Choose an appropriate icon for this category.'),
                        TextInput::make('description')
                            ->label('Description'),
                    ])->columns(3)
                    ->collapsible()

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('name')
                    ->label('Classification Name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('icon')
                    ->label('Icon')
                    ->formatStateUsing(fn($state) => $state),
                // TextColumn::make('description')
                //     ->label('Description')
                //     ->limit(50),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),

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
            'index' => Pages\ListFoodDivisions::route('/'),
            'create' => Pages\CreateFoodDivision::route('/create'),
            'edit' => Pages\EditFoodDivision::route('/{record}/edit'),
        ];
    }
    public static function shouldRegisterNavigation(): bool
    {
        return Filament::getCurrentPanel()?->getId() === 'management';
    }
}
