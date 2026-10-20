<?php

namespace Database\Seeders;

use App\Models\Equipment;
use App\Models\ServiceRecord;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Několik pevně daných zařízení, ať jsou v testovacích datech zastoupeny všechny stavy.
        $fixed = [
            ['name' => 'Aku vrtačka Makita DDF485', 'inventory_number' => 'INV-00001', 'status' => 'active', 'purchased_at' => '2022-03-15'],
            ['name' => 'Úhlová bruska Bosch GWS 18V', 'inventory_number' => 'INV-00002', 'status' => 'service', 'purchased_at' => '2021-07-01'],
            ['name' => 'Kompresor Güde 260/10/24', 'inventory_number' => 'INV-00003', 'status' => 'retired', 'purchased_at' => '2015-11-20'],
            ['name' => 'Svářečka Kemppi Minarc', 'inventory_number' => 'INV-00004', 'status' => 'active', 'purchased_at' => '2023-01-10'],
        ];

        foreach ($fixed as $data) {
            $equipment = Equipment::create($data);
            ServiceRecord::factory()->count(3)->create(['equipment_id' => $equipment->id]);
        }

        // Další náhodná zařízení s 0–5 servisními záznamy.
        Equipment::factory()->count(12)->create()->each(function (Equipment $equipment) {
            ServiceRecord::factory()->count(random_int(0, 5))->create(['equipment_id' => $equipment->id]);
        });
    }
}
