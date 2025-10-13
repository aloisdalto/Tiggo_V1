<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'latitude',
        'longitude'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    //Relacion con cada tabla de la bd
    // Relación 1 a 1 con TechnicianProfile
    public function technicianProfile()
    {
        return $this->hasOne(TechnicianProfile::class); 
    }

    // Relación con solicitudes hechas (como cliente)
    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class, 'cliente_id');
    }

    // Relación con calificaciones dadas (como cliente)
    public function ratingsGiven()
    {
        return $this->hasMany(Rating::class, 'cliente_id');
    }

    // Relación con calificaciones recibidas (como técnico)
    public function ratingsReceived()
    {
        return $this->hasMany(Rating::class, 'tecnico_id');
    }
}
