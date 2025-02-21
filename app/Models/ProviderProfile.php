<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderProfile extends Model
{
    protected $fillable = ['user_id', 'service_name', 'description', 'price_range'];


    //Egy providernek pontosan egy ProviderProfile-ja van
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
