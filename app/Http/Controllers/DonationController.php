<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Donation;
use Illuminate\Http\Request;



class DonationController extends Controller
{
    public function store(Request $request, $id)
{
    $campaign = Campaign::findOrFail($id);

    if (!$campaign->is_active) {
        return response()->json(['error' => 'Campaign closed'], 400);
    }

    $data = $request->validate([
        'amount' => ['required', 'numeric', 'min:0.01']
    ]);

    $amount = round((float)$data['amount'], 2);

    Donation::create([
        'campaign_id' => $id,
        'amount' => $amount
    ]);

    $campaign->current_amount = (float)$campaign->current_amount + $amount;

    if ($campaign->current_amount >= $campaign->goal_amount) {
        $campaign->is_active = false;
    }

    $campaign->save();

    return response()->json([
        'message' => 'Donation added'
    ], 201);
}

public function index($id)
{
    return response()->json(
        Donation::where('campaign_id', $id)->get()
    );
}
}