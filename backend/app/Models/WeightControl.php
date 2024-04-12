<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeightControl extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'weight'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
