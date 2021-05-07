<?php

namespace Database\Seeders;

use App\Models\ImageProduct;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        Product::truncate();
        ImageProduct::truncate();

        Product::factory()->count(10)->create();
        ImageProduct::factory()->count(100)->create();
    }
}
