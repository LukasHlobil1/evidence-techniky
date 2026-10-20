<?php

namespace Database\Factories;

use App\Models\Equipment;
use App\Models\ServiceRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceRecord>
 */
class ServiceRecordFactory extends Factory
{
    protected $model = ServiceRecord::class;

    public function definition(): array
    {
        return [
            'equipment_id' => Equipment::factory(),
            'description' => fake()->randomElement([
                'Výměna uhlíků motoru',
                'Pravidelná revize',
                'Výměna kabelu a vidlice',
                'Čištění a mazání převodovky',
                'Výměna ložisek',
                'Kalibrace měřidla',
                'Oprava spínače',
                'Výměna oleje a filtru',
            ]),
            'serviced_at' => fake()->dateTimeBetween('-3 years', 'now')->format('Y-m-d'),
            'cost' => fake()->randomFloat(2, 0, 8000),
        ];
    }
}
