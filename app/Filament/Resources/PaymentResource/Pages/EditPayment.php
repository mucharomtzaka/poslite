<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use Filament\Actions;
use App\Models\Order;
use Filament\Resources\Pages\EditRecord;

class EditPayment extends EditRecord
{
    protected static string $resource = PaymentResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['order_id'] = $data['order_id'] ?? 0;
        $order = Order::find($data['order_id']);
        $data['total_amount'] = $order->total_amount;
        $data['balance'] = $data['total_amount'] - $data['amount'];
        if ($data['total_amount'] == $data['amount']) {
            $data['status'] = 'paid';
        }  else {
            $data['status'] = 'pending';
        }
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['order_id'] = $data['order_id'] ?? 0;
        $order = Order::find($data['order_id']);
        $data['total_amount'] = $order->total_amount;
        
        if ($data['total_amount'] == $data['amount'] || $data['amount'] > 0) {
            $status = 'paid';
        }  else {
            $status = 'pending';
        }
        $order->update(['status' => $status]);
        return $data;
    }
    
}
