<?php

namespace Database\Factories;

use App\Models\Installment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Installment>
 */
class InstallmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Installment::class;

    public function definition(): array
    {
        return [
            'value' => $this->faker->randomFloat(2, 100, 1000),
            'installment_number' => 1,
            'due_date' => $this->faker->date()
        ];
    }
}
