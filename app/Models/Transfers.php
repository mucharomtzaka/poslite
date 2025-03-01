<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\ProductLocations;
use App\Models\Product;
use App\Models\InventoryLog;
use App\Models\Locations;

class Transfers extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'product_id',
        'from_location',
        'to_location',
        'quantity',
        'transfer_date',
        'reason',
        'user_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function to_location()
    {
        return $this->belongsTo(Locations::class,'to_location');
    }

    public function from_location()
    {
        return $this->belongsTo(Locations::class,'from_location');
    }

}
