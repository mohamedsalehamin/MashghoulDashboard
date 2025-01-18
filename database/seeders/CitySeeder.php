<?php

namespace Database\Seeders;

use App\ContentModule\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        City::truncate();
        $cities = File::get(base_path('/database/seeders/cities.json'));
        $cities = json_decode($cities, true);
        foreach ($cities as $city) {
            if (!isset($city['LKCityId']) || !isset($city['LKRegionId'])) {
                continue;
            }
            City::create([
                'id' => $city['LKCityId'],
                'status' => 1,
                'name' => [
                    'ar' => $city['LKCityAr'],
                    'en' => $city['LKCityEn'] ?? $city['LKCityAr'],
                ],
                'state_id' => $city['LKRegionId']
            ]);
        }
    }
}
