<?php

namespace Database\Seeders;

use App\ContentModule\Models\Country;
use App\ContentModule\Models\State;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Tasawk\Models\Location\Zone;

class ZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        State::truncate();
        $zones = File::get(base_path('/database/seeders/regions.json'));
        $zones = json_decode($zones, true);
        foreach ($zones as $zone) {
            State::create([
                'id' => $zone['LKRegionId'],
                'status' => 1,
                'name' => [
                    'ar' => $zone['LKRegionAr'],
                    'en' => $zone['LKRegionEn'],
                ],
                'country_id' => 1,

            ]);
        }
    }
}
