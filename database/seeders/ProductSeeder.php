<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Product::create([
            'category_id'=>1,
            'supplier_id'=>1,
            'name' => 'Sampo',
            'price_sale' => 10000.00,
            'price_purchase' =>5000.00,
            'sku' => '1234567890',
            'barcode' => '1234567890123',
            'stock_quantity' => 0,
            'min_stock_level' => 0,
            'description' => 'Sampo pantene',
        ]);
        Product::create([
            'category_id'=>1,
            'supplier_id'=>1,
            'name' => 'Tas',
            'price_sale' => 20000.00,
            'price_purchase' =>15000.00,
            'sku' => '908801001',
            'barcode' => '908801001',
            'stock_quantity' => 0,
            'min_stock_level' => 0,
            'description' => 'Sampo pantene',
        ]);
    }
}
