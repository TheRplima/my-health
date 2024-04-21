<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaterIntakeContainers extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'size',
        'icon',
        'active'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
