<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Supplier::create([
            'name' => 'Supplier',
            'contact_name' => 'Default',
            'address' => 'Default',
            'phone' => '0899791997917',
            'email' => 'default@gmail.com',
        ]);
    }
}
