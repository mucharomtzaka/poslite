<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseTransactionItem extends Model
{
    //
     use HasFactory;

    protected $fillable = [
        'purchase_transaction_id',
        'product_id',
        'quantity',
        'purchase_price',
    ];

    public function purchaseTransaction()
    {
        return $this->belongsTo(PurchaseTransaction::class,'purchase_transaction_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id');
    }
}
