<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'start',
        'end',
        'interval',
        'snooze',
        'disabled',
        'disabled_until',
    ];

    protected $casts = [
        'disabled' => 'boolean',
        'disabled_until' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function enable()
    {
        $this->disabled = false;
        $this->disabled_until = null;
        $this->save();
    }

    public function disable()
    {
        $this->disabled = true;
        $this->disabled_until = null;
        $this->save();
    }

    public function snooze(int $minutes)
    {
        $this->disabled = true;
        $this->disabled_until = now()->addMinutes($minutes);
        $this->save();
    }

    public function isDisabled()
    {
        return $this->disabled && !$this->isSnoozed();
    }

    public function isSnoozed()
    {
        return $this->disabled_until && now()->diffInMinutes($this->disabled_until) > 0;
    }
}
