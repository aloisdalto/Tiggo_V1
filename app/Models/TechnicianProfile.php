<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TechnicianProfile extends Model
{
    protected $fillable = [
        'user_id', 'description', 'average_rating', 'is_available', 'latitude', 'longitude'
    ];

    protected $casts = [
        'average_rating' => 'decimal:2',
        'is_available' => 'boolean',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Servicios que ofrece el técnico (muchos a muchos)
    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_technician');
    }

    // Solicitudes asignadas al técnico
    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class, 'tecnico_id');
    }
}
