<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Categories;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Categories::create([
            'name' =>'Default',
            'slug'=>'default',
            'description' => 'Category Default',
        ]);
        Categories::create([
            'name' =>'Electronics',
            'slug'=>'electronics',
            'description' => 'Electronics',
        ]);
    }
}
