<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Machine;
use App\Models\Tenant;

class MachineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenant = Tenant::first();
        if (!$tenant) {
            $this->command->error('No tenant found. Run DatabaseSeeder first.');
            return;
        }

        $machines = [
            ['nomor_mesin' => 'M-01', 'nama_mesin' => 'Mesin 1'],
            ['nomor_mesin' => 'M-02', 'nama_mesin' => 'Mesin 2'],
            ['nomor_mesin' => 'M-03', 'nama_mesin' => 'Mesin 3'],
            ['nomor_mesin' => 'M-04', 'nama_mesin' => 'Mesin 4'],
            ['nomor_mesin' => 'M-05', 'nama_mesin' => 'Mesin 5'],
            ['nomor_mesin' => 'M-06', 'nama_mesin' => 'Mesin 6'],
            ['nomor_mesin' => 'M-07', 'nama_mesin' => 'Mesin 7'],
            ['nomor_mesin' => 'M-08', 'nama_mesin' => 'Mesin 8'],
            ['nomor_mesin' => 'M-09', 'nama_mesin' => 'Mesin Gibrig'],
        ];

        foreach ($machines as $machine) {
            Machine::withoutGlobalScopes()->firstOrCreate([
                'nomor_mesin' => $machine['nomor_mesin'],
                'tenant_id' => $tenant->id,
            ], [
                'nama_mesin' => $machine['nama_mesin'],
            ]);
        }
    }
}
