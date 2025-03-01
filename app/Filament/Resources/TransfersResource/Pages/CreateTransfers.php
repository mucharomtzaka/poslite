<?php

namespace App\Filament\Resources\TransfersResource\Pages;

use App\Filament\Resources\TransfersResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Product;
use App\Models\ProductLocations;
use App\Models\InventoryLog;

class CreateTransfers extends CreateRecord
{
    protected static string $resource = TransfersResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void{
        $transfer = $this->record;
        if ($transfer->from_location === 0) {
            // Deduct from Product Master Stock
            $product = Product::find($transfer->product_id);
            if ($product && $product->stock_quantity >= $transfer->quantity) {
                $product->decrement('stock_quantity', $transfer->quantity);
            }
        } else {
            // Deduct from specific product location
            $fromLocation = ProductLocations::where('product_id', $transfer->product_id)
                ->where('location', $transfer->from_location)
                ->first();

            if ($fromLocation && $fromLocation->stock_quantity >= $transfer->quantity) {
                $fromLocation->decrement('stock_quantity', $transfer->quantity);
            }
        }

        // Add stock to the new location
        $toLocation = ProductLocations::firstOrCreate([
            'product_id' => $transfer->product_id,
            'location_id' => $transfer->to_location,
        ], [
            'stock_quantity' => 0,
        ]);

        $toLocation->increment('stock_quantity', $transfer->quantity);

        $inventoryLogs[] = [
            'product_id' => $transfer->product_id,
            'change_type' => 'Transfer',
            'quantity_change' => $transfer->quantity,
            'reason' => 'Transfer Stock ',
            'logged_by' => auth()->id(),
            'log_date' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
