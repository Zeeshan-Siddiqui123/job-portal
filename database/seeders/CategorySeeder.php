<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $categories = [
                ['name' => 'Software Engineering', 'slug' => 'software-engineering', 'icon' => 'fas fa-code'],
                ['name' => 'Frontend & UI/UX', 'slug' => 'frontend-ui-ux', 'icon' => 'fas fa-paint-brush'],
                ['name' => 'Database & Cloud DevOps', 'slug' => 'database-cloud', 'icon' => 'fas fa-server'],
                ['name' => 'Product & Project Management', 'slug' => 'product-management', 'icon' => 'fas fa-tasks'],
            ];

            foreach ($categories as $category) {
                Category::firstOrCreate(['slug' => $category['slug']], $category);
            }
        });
    }
}
