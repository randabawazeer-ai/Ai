<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'طعام ومشروبات', 'icon' => 'utensils', 'type' => 'expense'],
            ['name' => 'مواصلات', 'icon' => 'car', 'type' => 'expense'],
            ['name' => 'سكن', 'icon' => 'home', 'type' => 'expense'],
            ['name' => 'فواتير ومرافق', 'icon' => 'zap', 'type' => 'expense'],
            ['name' => 'ترفيه', 'icon' => 'tv', 'type' => 'expense'],
            ['name' => 'تسوق', 'icon' => 'shopping-bag', 'type' => 'expense'],
            ['name' => 'صحة', 'icon' => 'heart', 'type' => 'expense'],
            ['name' => 'تعليم', 'icon' => 'graduation-cap', 'type' => 'expense'],
            ['name' => 'راتب', 'icon' => 'briefcase', 'type' => 'income'],
            ['name' => 'عمل حر', 'icon' => 'laptop', 'type' => 'income'],
            ['name' => 'استثمار', 'icon' => 'trending-up', 'type' => 'income'],
            ['name' => 'هدايا', 'icon' => 'gift', 'type' => 'both'],
            ['name' => 'أخرى', 'icon' => 'more-horizontal', 'type' => 'both'],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'icon' => $category['icon'],
                'type' => $category['type'],
                'is_default' => true,
            ]);
        }
    }
}
