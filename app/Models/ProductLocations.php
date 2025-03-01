<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\Locations;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductLocations extends Model
{
    //

    use HasFactory;

    protected $fillable = [
        'product_id',
        'location_id',
        'stock_quantity',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id');
    }

    public function location()
    {
        return $this->belongsTo(Locations::class,'location_id');
    }
}
