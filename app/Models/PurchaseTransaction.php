<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseTransaction extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'purchase_date',
        'user_id',
        'quantity',
        'purchase_price',
    ];

    public function items()
    {
        return $this->hasMany(PurchaseTransactionItem::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class,'supplier_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
