<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransfersResource\Pages;
use App\Filament\Resources\TransfersResource\RelationManagers;
use App\Models\Transfers;
use App\Models\ProductLocations;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransfersResource extends Resource
{
    protected static ?string $model = Transfers::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Inventory';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Select::make('from_location')
                    ->options(
                        ['0' => 'Master Stock'] +
                        ProductLocations::with('location')->get()
                            ->mapWithKeys(fn($pl) => [$pl->location_id => $pl->location->name ?? 'Unknown Location'])
                            ->toArray()
                    )
                    ->required(),
                Select::make('to_location')
                    ->options(
                        ProductLocations::with('location')->get()
                            ->mapWithKeys(fn($pl) => [$pl->location_id => $pl->location->name ?? 'Unknown Location'])
                            ->toArray()
                    ),

                Select::make('product_id')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->required(),
                
                TextInput::make('quantity')
                    ->numeric()
                    ->required(),
                DateTimePicker::make('transfer_date')
                    ->default(now())
                    ->required(),
                TextInput::make('reason')->default('Stock Transfer'),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()->default(1)
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('id')->sortable(),
                TextColumn::make('product.name')->label('Product')->sortable(),
                TextColumn::make('from_location')->sortable(),
                TextColumn::make('to_location')->sortable(),
                TextColumn::make('quantity')->sortable(),
                TextColumn::make('transfer_date')->sortable()->dateTime(),
                TextColumn::make('user.name')->label('Transferred By')->sortable(),
            ])
            ->filters([
                //
                
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
            'index' => Pages\ListTransfers::route('/'),
            'create' => Pages\CreateTransfers::route('/create'),
        ];
    }
}
