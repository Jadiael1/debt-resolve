<?php

namespace Database\Factories;

use App\Models\Charge;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Charge>
 */
class ChargeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Charge::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->text(100),
            'description' => $this->faker->paragraph(1),
            'amount' => $this->faker->randomFloat(2, 100, 1000),
            'installments_number' => $this->faker->numberBetween(1, 12),
            'due_day' => $this->faker->numberBetween(1, 28),
        ];
    }
}
