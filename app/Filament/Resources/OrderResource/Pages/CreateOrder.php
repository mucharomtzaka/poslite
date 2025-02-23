<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Product; 
use App\Models\Payment;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;


class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {

        $data['items'] = $data['items'] ?? [];
        
            foreach ($data['items'] as &$item) {
                $product = Product::find($item['product_id']);

                // Ensure product exists and price is valid
                if (!$product) {
                    throw new \Exception("Product not found for ID: {$item['product_id']}");
                }

                $item['unit_price'] = $product->price;
                $item['total_price'] = (float) $product->price * $item['quantity'];
            }

            unset($item); // Unset the reference
           
            $data['order_date'] = now();
            
        return $data ;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
