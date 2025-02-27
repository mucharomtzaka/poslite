<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Locations;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Locations::create([
            'name' => 'Toko 1',
            'set_default' => 1,
        ]);
        Locations::create([
            'name' => 'Main Warehouse',
            'set_default' => 0,
        ]);
    }
}
