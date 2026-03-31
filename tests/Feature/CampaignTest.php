<?php

use App\Models\Campaign;
use App\Models\Donation;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('campaigns endpoint werkt', function () {
    $response = $this->getJson('/api/campaigns');

    $response->assertStatus(200);
});

test('kan campaign aanmaken', function () {
    $response = $this->postJson('/api/campaigns', [
        'title' => 'Test Campaign',
        'description' => 'Test description',
        'goal_amount' => 100,
    ]);

    $response->assertStatus(201);
});

test('validatie faalt zonder title', function () {
    $response = $this->postJson('/api/campaigns', [
        'description' => 'Test description',
        'goal_amount' => 100,
    ]);

    $response->assertStatus(422);
});

test('kan campaign ophalen', function () {
    $campaign = Campaign::factory()->create();

    $response = $this->getJson("/api/campaigns/{$campaign->id}");

    $response->assertStatus(200);
});

test('kan campaign updaten', function () {
    $campaign = Campaign::factory()->create();

    $response = $this->putJson("/api/campaigns/{$campaign->id}", [
        'title' => 'Updated title'
    ]);

    $response->assertStatus(200);
});

test('kan campaign verwijderen', function () {
    $campaign = Campaign::factory()->create();

    $response = $this->deleteJson("/api/campaigns/{$campaign->id}");

    $response->assertStatus(200);
});

test('kan doneren aan campaign', function () {
    $campaign = Campaign::factory()->create([
        'is_active' => true,
        'current_amount' => 0,
        'goal_amount' => 100,
    ]);

    $response = $this->postJson("/api/campaigns/{$campaign->id}/donate", [
        'amount' => 25
    ]);

    $response->assertStatus(201);

    expect((float) $campaign->fresh()->current_amount)->toBe(25.0);
});

test('donatie met negatieve waarde faalt', function () {
    $campaign = Campaign::factory()->create([
        'is_active' => true
    ]);

    $response = $this->postJson("/api/campaigns/{$campaign->id}/donate", [
        'amount' => -10
    ]);

    $response->assertStatus(422);
});

test('campaign sluit bij behalen doelbedrag', function () {
    $campaign = Campaign::factory()->create([
        'is_active' => true,
        'current_amount' => 90,
        'goal_amount' => 100,
    ]);

    $this->postJson("/api/campaigns/{$campaign->id}/donate", [
        'amount' => 20
    ]);

    expect($campaign->fresh()->is_active)->toBeFalse();
});

test('kan geen donatie doen op gesloten campaign', function () {
    $campaign = Campaign::factory()->create([
        'is_active' => false
    ]);

    $response = $this->postJson("/api/campaigns/{$campaign->id}/donate", [
        'amount' => 10
    ]);

    $response->assertStatus(400);
});

test('kan donaties ophalen van campaign', function () {
    $campaign = Campaign::factory()->create();

    Donation::create([
        'campaign_id' => $campaign->id,
        'amount' => 10
    ]);

    Donation::create([
        'campaign_id' => $campaign->id,
        'amount' => 20
    ]);

    $response = $this->getJson("/api/campaigns/{$campaign->id}/donations");

    $response->assertStatus(200)
             ->assertJsonCount(2);
});