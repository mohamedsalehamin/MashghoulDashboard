<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\ContentModule\Models\Page;

class PageSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        Page::truncate();
        $list = [
            ['ar' => 'من نحن', 'en' => 'about us'],
            ['ar' => 'الشروط والأحكام', 'en' => 'terms and conditions'],
            ['ar' => 'سياسة الخصوصية', 'en' => 'privacy policy'],
            ['ar' => 'الأسئلة الشائعة', 'en' => 'FAQ'],
        ];
        foreach ($list as $item) {
          Page::create(['title' => $item, 'description' => $item]);
        }
    }
}
