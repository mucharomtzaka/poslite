<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;

class ProductStockWidget extends BaseWidget
{
    protected static ?string $heading = 'Low Stock Products';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::where('stock_quantity', '<', 10)
            )
            ->columns([
                TextColumn::make('name')->label('Product Name')->sortable(),
                TextColumn::make('stock_quantity')
                    ->label('Stock')
                    ->sortable()
                    ->color(fn ($record) => $record->stock_quantity < 5 ? 'danger' : 'warning'), 
            ])->defaultPaginationPageOption(5);
    }
}
