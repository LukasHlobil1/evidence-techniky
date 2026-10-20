<?php

namespace Database\Factories;

use App\Models\Equipment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Equipment>
 */
class EquipmentFactory extends Factory
{
    protected $model = Equipment::class;

    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'Aku vrtačka', 'Úhlová bruska', 'Kompresor', 'Svářečka', 'Laserový dálkoměr',
                'Elektrocentrála', 'Vibrační deska', 'Míchačka', 'Pokosová pila', 'Tlaková myčka',
            ]) . ' ' . fake()->bothify('??-###'),
            'inventory_number' => sprintf('INV-%05d', fake()->unique()->numberBetween(1000, 99999)),
            'status' => fake()->randomElement(array_keys(Equipment::STATUSES)),
            'purchased_at' => fake()->dateTimeBetween('-8 years', '-1 month')->format('Y-m-d'),
        ];
    }
}
