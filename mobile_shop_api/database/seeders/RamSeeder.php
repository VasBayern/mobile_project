<?php

namespace Database\Seeders;

use App\Models\Ram;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('rams')->truncate();

        $rams = [1, 2, 3, 4, 6, 8, 12, 16, 32, 64];

        foreach ($rams as $ram) {
            $name = [
                'name'  => $ram
            ];
            Ram::create($name);
        }
    }
}
