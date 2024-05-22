<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhysicalActivity extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'description', 'sport_id', 'calories_burned', 'date', 'start_time', 'end_time', 'duration', 'effort_level', 'distance', 'speed', 'steps', 'pace', 'observations'];

    public function sport()
    {
        return $this->belongsTo(PhysicalActivitySport::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
