<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory,SoftDeletes;

    // Define the primary key
    protected $primaryKey = 'product_id';

    // Define fillable fields
    protected $fillable = [
        'category_id',
        'supplier_id',
        'name',
        'price_sale',
        'price_purchase',
        'sku',
        'barcode',
        'stock_quantity',
        'min_stock_level',
        'description',
    ];

    protected $casts = [
        'price_sale' => 'decimal:2',
        'price_purchase' => 'decimal:2',
        'stock_quantity' => 'integer',
        'min_stock_level' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Categories::class, 'category_id');
    }

    public function supplier()
    {
        return $this->HasOne(Supplier::class, 'supplier_id');
    }
}
