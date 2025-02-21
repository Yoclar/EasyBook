<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = ['user_id', 'provider_id', 'start_time', 'end_time', 'status'];

    //Egy foglalás tartozik egy user-hez.
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    //Egy foglalás tartozik egy provider-hez.
    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }
}
