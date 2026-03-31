<?php

namespace App\Models;

use App\Models\Campaign;
use Illuminate\Database\Eloquent\Model;


class Donation extends Model
{
    protected $fillable = ['campaign_id', 'amount'];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
