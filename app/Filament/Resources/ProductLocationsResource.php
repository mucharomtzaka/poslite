<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductLocationsResource\Pages;
use App\Filament\Resources\ProductLocationsResource\RelationManagers;
use App\Models\ProductLocations;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductLocationsResource extends Resource
{
    protected static ?string $model = ProductLocations::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-left';

    protected static ?string $navigationGroup = 'Inventory';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Section::make('Location Details')
                ->schema([
                    Select::make('location_id')
                        ->relationship('location', 'name')
                        ->searchable()
                        ->default(1)
                        ->required(),
                ])
                ->columnSpan(1), // This ensures it takes up 1 column

            Section::make('Product Stock')
                ->schema([
                    Repeater::make('product_locations')
                        ->schema([
                            Grid::make(2) // Creates a two-column layout
                                ->schema([
                                    Select::make('product_id')
                                        ->relationship('product', 'name')
                                        ->searchable()
                                        ->required(),

                                    TextInput::make('stock_quantity')
                                        ->numeric()
                                        ->required()
                                        ->default(0),
                                ]),
                        ])
                        ->minItems(1)
                        ->collapsible(),
                ])
                ->columnSpan(2),  
            ]) ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('product.name')->label('Product')->sortable()->searchable(),
                TextColumn::make('location.name')->label('Location')->sortable()->searchable(),
                 TextColumn::make('stock_quantity')->label('Stock Quantity')->sortable(),
            ])
            ->filters([
                //
                SelectFilter::make('location_id')
                ->label('Filter by Location')
                ->relationship('location', 'name') // Fetch locations dynamically
                ->searchable(),
            ])
            ->actions([
               
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
            'index' => Pages\ListProductLocations::route('/'),
            'create' => Pages\CreateProductLocations::route('/create'),
            'edit' => Pages\EditProductLocations::route('/{record}/edit'),
        ];
    }
}
