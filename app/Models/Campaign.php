<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Campaign extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'goal_amount',
        'current_amount',
        'is_active'
    ];
    
    protected $casts = [
    'is_active' => 'boolean',
];

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }
}