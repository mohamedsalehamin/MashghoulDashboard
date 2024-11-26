<?php

namespace Database\Seeders;

use App\CatalogModule\Models\Specialization;
use Illuminate\Database\Seeder;

class SpecialtySeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        Specialization::truncate();
        $specialties = json_decode(file_get_contents(base_path('database/seeders/data/specialties.json')), true);
        foreach ($specialties as $specialty) {
            $record = Specialization::create(['name' => $specialty['name']]);
            $record->addMedia(public_path("assets/" . $specialty['image']))
                ->preservingOriginal()
                ->toMediaCollection();
        }
    }
}
