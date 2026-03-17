<?php

namespace Database\Seeders;

use App\Models\WasteCategory;
use Illuminate\Database\Seeder;

class WasteCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Plastik', 'description' => 'Sampah plastik', 'unit' => 'kg'],
            ['name' => 'Kertas', 'description' => 'Sampah kertas dan karton', 'unit' => 'kg'],
            ['name' => 'Logam', 'description' => 'Sampah logam', 'unit' => 'kg'],
            ['name' => 'Kaca', 'description' => 'Sampah kaca', 'unit' => 'kg'],
            ['name' => 'Organik', 'description' => 'Sampah organik', 'unit' => 'kg'],
        ];

        foreach ($categories as $category) {
            WasteCategory::firstOrCreate(
                ['name' => $category['name'], 'tenant_id' => 1],
                array_merge($category, ['tenant_id' => 1, 'is_active' => true])
            );
        }
    }
}
