<?php

namespace Database\Factories;

use App\Models\Campaign;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Campaign>
 */
class CampaignFactory extends Factory
{
public function definition(): array
{
    return [
        'title' => $this->faker->sentence(),
        'description' => $this->faker->paragraph(),
        'goal_amount' => 100,
        'current_amount' => 0,
        'is_active' => true,
    ];
}
}
