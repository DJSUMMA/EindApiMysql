<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Campaign;

class CampaignSeeder extends Seeder
{
    public function run(): void
    {
        Campaign::create([
            'title' => 'Save the Rainforest',
            'description' => 'Support environmental protection.',
            'goal_amount' => 1000,
            'current_amount' => 250,
            'is_active' => true,
        ]);

        Campaign::create([
            'title' => 'Medical Aid',
            'description' => 'Help provide medical supplies.',
            'goal_amount' => 5000,
            'current_amount' => 3200,
            'is_active' => true,
        ]);

        Campaign::create([
            'title' => 'Education Fund',
            'description' => 'Support education for children.',
            'goal_amount' => 2000,
            'current_amount' => 2000,
            'is_active' => false,
        ]);
    }
}