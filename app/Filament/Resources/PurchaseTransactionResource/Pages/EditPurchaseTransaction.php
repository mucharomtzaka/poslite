<?php

namespace App\Filament\Resources\PurchaseTransactionResource\Pages;

use App\Filament\Resources\PurchaseTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseTransaction;
use App\Models\PurchaseTransactionItem;
use App\Models\Product;
use App\Models\InventoryLog;

class EditPurchaseTransaction extends EditRecord
{
    protected static string $resource = PurchaseTransactionResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $items = PurchaseTransactionItem::where('purchase_transaction_id', $data['id'])->get();

        $totalPrice = 0;
        $totalQuantity = 0;
        $itemDetails = [];

        foreach ($items as $item) {
            $totalPrice += $item->quantity * $item->purchase_price;
            $totalQuantity += $item->quantity;

            $itemDetails[] = [
                'product_id'    => $item->product_id,
                'quantity'      => $item->quantity,
                'purchase_price'=> $item->purchase_price,
                'total_price'   => $item->quantity * $item->purchase_price,
            ];
        }

        $data['total_purchase'] = $totalPrice;
        $data['total_quantity'] = $totalQuantity;
        $data['items'] = $itemDetails;
        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return DB::transaction(function () use ($record, $data) {

            // Validate 'item_detail'
            if (empty($data['item_detail']) || !is_array($data['item_detail'])) {
                throw new \Exception("No items provided for the purchase transaction.");
            }

            $totalPurchase = 0;
            $totalQuantity = 0;

            // Fetch existing items & reverse stock changes
            $existingItems = $record->items()->get();
            foreach ($existingItems as $existingItem) {
                Product::where('product_id', $existingItem->product_id)->decrement('stock_quantity', $existingItem->quantity);
            }
            // Delete existing items & logs
            $record->items()->delete();
            
            InventoryLog::where('purchase_transaction_id', $record->id)->delete();

            // Prepare new items
            $itemsToInsert = [];
            $inventoryLogs = [];
            $productUpdates = [];

            foreach ($data['item_detail'] as $item) {
                if (!isset($item['product_id'], $item['quantity'], $item['purchase_price'])) {
                    throw new \Exception("Invalid item data. Each item must have 'product_id', 'quantity', and 'purchase_price'.");
                }

                $itemsToInsert[] = [
                    'purchase_transaction_id' => $record->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'purchase_price' => $item['purchase_price'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Calculate totals
                $totalPurchase += $item['quantity'] * $item['purchase_price'];
                $totalQuantity += $item['quantity'];

                // Add inventory log data
                $inventoryLogs[] = [
                    'purchase_transaction_id' => $record->id,
                    'product_id' => $item['product_id'],
                    'change_type' => 'Updated',
                    'quantity_change' => $item['quantity'],
                    'reason' => 'Updated Purchase Transaction',
                    'logged_by' => auth()->id(),
                    'log_date' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Collect product stock updates
                if (!isset($productUpdates[$item['product_id']])) {
                    $productUpdates[$item['product_id']] = ['quantity' => 0, 'purchase_price' => 0];
                }
                $productUpdates[$item['product_id']]['quantity'] += $item['quantity'];
                $productUpdates[$item['product_id']]['purchase_price'] = $item['purchase_price'];
            }

            // Insert new items
            PurchaseTransactionItem::insert($itemsToInsert);

            // Insert inventory logs
            InventoryLog::insert($inventoryLogs);

            // Update product stock & purchase price
            foreach ($productUpdates as $productId => $update) {
                Product::where('product_id', $productId)->increment('stock_quantity', $update['quantity'], [
                    'price_purchase' => $update['purchase_price'],
                    'updated_at' => now(),
                ]);
            }

            // Update purchase transaction details
            $record->update([
                'supplier_id'    => $data['supplier_id'],
                'purchase_date'  => $data['purchase_date'],
                'purchase_price' => $totalPurchase,
                'quantity'       => $totalQuantity,
            ]);

            return $record;
        });
    }
}
