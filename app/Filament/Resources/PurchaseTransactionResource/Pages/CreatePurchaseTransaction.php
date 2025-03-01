<?php
namespace App\Filament\Resources\PurchaseTransactionResource\Pages;

use App\Filament\Resources\PurchaseTransactionResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\InventoryLog;
use App\Models\Product;
use App\Models\PurchaseTransaction;
use App\Models\PurchaseTransactionItem;
use Illuminate\Database\Eloquent\Model;

class CreatePurchaseTransaction extends CreateRecord
{
    protected static string $resource = PurchaseTransactionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array{
        $data['quantity'] = $data['total_quantity'];
        $data['purchase_price'] = $data['total_purchase'];
        $data['user_id'] = auth()->id();
        return $data;
    }

    protected function afterCreate(): void
    {
        $purchase = $this->record;

        Log::info('Filament: PurchaseTransaction created!', ['id' => $purchase->id]);

        foreach ($purchase->items as $item) {
            Log::info('Filament: Processing item: ', ['product_id' => $item['product_id'], 'quantity' => $item['quantity']]);

            // Update product stock
            $product = Product::find($item['product_id']);
            if ($product) {
                $product->increment('stock_quantity', $item['quantity']);
                $product->update(['price_purchase' => $item['purchase_price']]);
            }

            // Create inventory log
            InventoryLog::create([
                'purchase_transaction_id' => $purchase->id,
                'product_id'              => $item['product_id'],
                'quantity_change'         => $item['quantity'],
                'change_type'             => 'purchase',
                'reason'                  => 'Purchase Stock Added',
                'logged_by'               => auth()->id(),
                'log_date'                => now(),
            ]);
        }
    }
}
