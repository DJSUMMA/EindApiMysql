<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function index()
    {
        return response()->json(Campaign::all());
    }

    public function store(Request $request)
    {
    $data = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'goal_amount' => [
            'required',
            'numeric',
            'min:1',
            'regex:/^\d+(\.\d{1,2})?$/'
        ]
    ]);

        // forceer 2 decimalen
        $data['goal_amount'] = round($data['goal_amount'], 2);

        $campaign = Campaign::create($data);

        return response()->json($campaign, 201);
    }

    public function show($id)
    {
        return response()->json(Campaign::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $campaign = Campaign::findOrFail($id);

        $data = $request->validate([
            'title' => 'sometimes|string',
            'description' => 'nullable|string',
            'goal_amount' => [
                'sometimes',
                'numeric',
                'regex:/^\d+(\.\d{1,2})?$/'
            ],
            'is_active' => 'sometimes|boolean'
        ]);

        if (isset($data['goal_amount'])) {
            $data['goal_amount'] = round($data['goal_amount'], 2);
        }

        $campaign->update($data);

        return response()->json($campaign);
    }

    public function destroy($id)
    {
        Campaign::findOrFail($id)->delete();

        return response()->json(['message' => 'Deleted']);
    }
}