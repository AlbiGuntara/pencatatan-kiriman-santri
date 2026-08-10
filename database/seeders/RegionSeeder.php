<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        $regions = [
            ['name' => 'IKSASS Bali', 'code' => 'BALI'],
            ['name' => 'IKSASS Lombok', 'code' => 'LOMBOK'],
            ['name' => 'IKSASS Jakarta', 'code' => 'JKT'],
            ['name' => 'IKSASS Situbondo', 'code' => 'STB'],
            ['name' => 'IKSASS Surabaya', 'code' => 'SBY'],
            ['name' => 'IKSASS Makassar', 'code' => 'MKS'],
            ['name' => 'IKSASS Medan', 'code' => 'MDN'],
            ['name' => 'IKSASS Samarinda', 'code' => 'SMR'],
        ];

        foreach ($regions as $region) {
            Region::query()->updateOrCreate(['code' => $region['code']], $region);
        }
    }
}
