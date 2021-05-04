<?php

namespace Database\Seeders;

use App\Models\Rom;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('roms')->truncate();

        $roms = [1, 2, 4, 8, 16, 32, 64, 128, 256, 512];

        foreach ($roms as $rom) {
            $name = [
                'name'  => $rom
            ];
            Rom::create($name);
        }
    }
}
