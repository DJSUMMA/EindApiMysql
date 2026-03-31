<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\DonationController;

Route::apiResource('campaigns', CampaignController::class);

Route::post('campaigns/{id}/donate', [DonationController::class, 'store']);
Route::get('campaigns/{id}/donations', [DonationController::class, 'index']);