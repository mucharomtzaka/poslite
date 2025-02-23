<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;

    protected $table = "customers";
    protected $softDeletes = true;

    // Define the primary key
    protected $primaryKey = 'customer_id';

    // Define fillable fields
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'loyalty_points',
    ];

    // Define relationships (if any)
    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }
}
