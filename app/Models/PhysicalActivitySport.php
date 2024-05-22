<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhysicalActivitySport extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'category_id', 'calories_burned_per_minute', 'metabolic_equivalent'];

    public function category()
    {
        return $this->belongsTo(PhysicalActivityCategory::class);
    }

    public function physicalActivities()
    {
        return $this->hasMany(PhysicalActivity::class);
    }

    public function getPhysicalActivityCountAttribute()
    {
        return $this->physicalActivities->count();
    }
}
