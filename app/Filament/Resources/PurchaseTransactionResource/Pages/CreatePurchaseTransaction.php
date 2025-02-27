<?php
namespace App\Filament\Resources\PurchaseTransactionResource\Pages;

use App\Filament\Resources\PurchaseTransactionResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
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
}
