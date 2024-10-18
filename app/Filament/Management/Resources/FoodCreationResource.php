<?php

namespace App\Filament\Management\Resources;

use App\Filament\Management\Resources\FoodCreationResource\Pages;
use App\Filament\Management\Resources\FoodCreationResource\RelationManagers;
use App\Models\FoodCreation;
use Filament\Facades\Filament;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;

use Filament\Tables\Actions\DeleteBulkAction;

use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use App\Models\FoodDivision;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;


class FoodCreationResource extends Resource
{
    protected static ?string $model = FoodCreation::class;

    protected static ?string $navigationIcon = 'heroicon-o-cake';
    protected static ?string $navigationGroup = 'Food Section';

    public static function form(Form $form): Form
    {
        // Helper function to format names
        $formatName = function ($state, callable $set, $field) {
            $formatted = ucwords(strtolower($state));
            $set($field, $formatted);
        };
        $categories = FoodDivision::all()->pluck('name', 'id')->toArray();

        return $form
            ->schema([
                Section::make('')
                    ->schema([
                        Select::make('food_division_id')
                            ->label('Food Division')
                            ->searchable()
                            ->options($categories)
                            ->required(),


                        TextInput::make('name')
                            ->label('Food Name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('price')
                            ->label('Price (₦)')
                            ->required()
                            ->placeholder('Enter price'),

                        FileUpload::make('image')
                            ->label('Food Image')
                            ->directory('food-division')
                            ->image()
                            ->maxSize(1024)
                            ->placeholder('Upload image'),

                        Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->placeholder('Enter a brief description')
                            ->maxLength(500),

                    ])->columns(3)
                    ->collapsible()

            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Food Image')
                    ->square()
                    ->size(40),

                TextColumn::make('name')
                    ->label('Food Name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('foodDivision.name')
                    ->label('Division')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('price')
                    ->label('Price (₦)')
                    ->sortable(),

            ])
            ->filters([
                SelectFilter::make('food_division_id')
                    ->label('Food Division')
                    ->relationship('foodDivision', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->bulkActions([
                DeleteBulkAction::make(),
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
            'index' => Pages\ListFoodCreations::route('/'),
            'create' => Pages\CreateFoodCreation::route('/create'),
            'edit' => Pages\EditFoodCreation::route('/{record}/edit'),
        ];
    }
    public static function shouldRegisterNavigation(): bool
    {
        return Filament::getCurrentPanel()?->getId() === 'management';
    }
}
