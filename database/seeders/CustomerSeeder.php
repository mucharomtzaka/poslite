<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Customer::create([
            'first_name' =>'Customer',
            'last_name' => 'Default',
            'email' => 'default@gmail.com',
            'phone' => '0001808011',
            'address' => 'Default',
            'loyalty_points' => 0,
        ]);
    }
}
