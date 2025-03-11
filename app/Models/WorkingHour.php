<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkingHour extends Model
{
    protected $fillable = ['provider_id', 'day', 'open_time', 'close_time'];

    // Egy WorkingHour egy provider-hez tartozik.
    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }
}
