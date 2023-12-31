<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AmoCrmLead>
 */
class AmoCRMLeadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'direction' => 'терапия',
            'specDoc'   => 'Врач-терапевт',
            'patID' => 1,
            'fioDoc'    => fake()->name(1),
            'billID'    => fake()->numberBetween(1000, 19990),
            'billSum'   => fake()->numberBetween(3000, 10000),
            'offers'    => 'Общий анализ крови',
            'managerName'   => fake()->name,
            'amoManagerID'  => fake()->numberBetween(1000, 19990),
            'declareCall'  => fake()->boolean,
            'filial'    => fake()->address,
            'date'  => fake()->date,
            'created_at'    => time(),
            'updated_at'    => time(),
        ];
    }
}
