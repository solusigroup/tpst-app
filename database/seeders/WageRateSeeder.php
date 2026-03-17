<?php

namespace Database\Seeders;

use App\Models\WageRate;
use App\Models\WasteCategory;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class WageRateSeeder extends Seeder
{
    public function run(): void
    {
        $categories = WasteCategory::where('tenant_id', 1)->get();

        $rates = [
            'Plastik' => 500,
            'Kertas' => 300,
            'Logam' => 1000,
            'Kaca' => 200,
            'Organik' => 50,
        ];

        foreach ($categories as $category) {
            $rate = $rates[$category->name] ?? 100;
            
            WageRate::firstOrCreate(
                ['waste_category_id' => $category->id, 'tenant_id' => 1, 'effective_date' => Carbon::now()->format('Y-m-d')],
                [
                    'rate_per_unit' => $rate,
                    'effective_date' => Carbon::now()->format('Y-m-d'),
                    'is_active' => true,
                ]
            );
        }
    }
}
