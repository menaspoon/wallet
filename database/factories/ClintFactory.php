<?php

namespace Database\Factories;

use App\Models\Clint;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Clint>
 */
class ClintFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'internal_referance' => $this->faker->unique()->uuid(),
        ];
    }
}
