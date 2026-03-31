<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Campaign;

class CampaignSeeder extends Seeder
{
    public function run(): void
    {
        Campaign::create([
            'title' => 'First Campaign',
            'description' => 'Test campaign',
            'goal_amount' => 100,
            'current_amount' => 0,
            'is_active' => true,
        ]);

        Campaign::factory()->count(5)->create();
    }
}