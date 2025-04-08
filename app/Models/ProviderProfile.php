<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderProfile extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'profile_image', 'service_name', 'description', 'website', 'address'];

    // Egy providernek pontosan egy ProviderProfile-ja van
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function workingHours()
    {
        return $this->hasMany(WorkingHour::class);
    }
}
