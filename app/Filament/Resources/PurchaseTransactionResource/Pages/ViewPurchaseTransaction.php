<?php

namespace App\Filament\Resources\PurchaseTransactionResource\Pages;

use App\Filament\Resources\PurchaseTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;

class ViewPurchaseTransaction extends ViewRecord
{
    protected static string $resource = PurchaseTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Purchase Details')
                    ->schema([
                        TextEntry::make('supplier.name')
                            ->label('Supplier')
                            ->badge(),
                        TextEntry::make('purchase_date')
                            ->label('Purchase Date')
                            ->date(),
                        TextEntry::make('user.name')
                            ->label('Processed By')
                            ->badge(),
                        TextEntry::make('purchase_price')
                            ->label('Total Amount')
                            ->money('idr'),
                        TextEntry::make('quantity')
                            ->label('Total Items')
                            ->numeric(),
                    ])
                    ->columns(2),

                Section::make('Purchased Items')
                    ->schema([
                        RepeatableEntry::make('items')
                            ->schema([
                                TextEntry::make('product.name')
                                    ->label('Product'),
                                TextEntry::make('quantity')
                                    ->label('Quantity'),
                                TextEntry::make('purchase_price')
                                    ->label('Purchase Price')
                                    ->money('idr'),
                            ])
                            ->columns(3),
                    ]),
            ]);
    }
}
