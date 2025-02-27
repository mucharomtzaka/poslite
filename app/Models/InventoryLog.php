<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryLog extends Model
{
    //
    protected $primaryKey = 'log_id';

    protected $fillable = [
        'product_id',
        'change_type',
        'quantity_change',
        'reason',
        'logged_by',
        'log_date'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'logged_by');
    }
}
