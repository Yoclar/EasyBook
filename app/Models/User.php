<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'is_google_user',
        'google_access_token',
        'google_refresh_token',
        'google_token_expires_at',

    ];

    // Ha provider, akkor lehet egy ProviderProfile-ja.
    public function providerProfile()
    {
        return $this->hasOne(ProviderProfile::class, 'user_id');
    }

    // Több időpontfoglalása (appointments) lehet.
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'user_id');
    }

    // Ha provider, akkor több foglalást is kaphat (receivedAppointments).
    public function receivedAppointments()
    {
        return $this->hasMany(Appointment::class, 'provider_id');
    }

    // Ha provider, akkor több munkaideje (workingHours) lehet.
    public function workingHours()
    {
        return $this->hasMany(WorkingHour::class, 'provider_id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
