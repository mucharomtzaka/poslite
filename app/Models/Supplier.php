<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
    //
    use HasFactory;

    protected $table = "suppliers";

    // Define the primary key
    protected $primaryKey = 'supplier_id';
    protected $softdeletes = true;

    // Define fillable fields
    protected $fillable = [
        'name',
        'contact_name',
        'address',
        'phone',
        'email',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'supplier_id');
    }
}
