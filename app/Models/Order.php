<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\hasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Locations;

class Order extends Model
{
    use SoftDeletes;
    
    protected $primaryKey = 'order_id';

    protected $fillable = [
        'customer_id',
        'order_date',
        'total_amount',
        'status',
        'total_price',
        'location_id'
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class,'order_id');
    }

    public function payments(): hasOne
    {
        return $this->hasOne(Payment::class, 'order_id');
    } 

    public function location(): BelongsTo
    {
        return $this->belongsTo(Locations::class, 'location_id');
    }
}
