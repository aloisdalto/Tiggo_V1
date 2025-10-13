<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['name', 'description'];

    // Técnicos que ofrecen este servicio
    public function technicians()
    {
        return $this->belongsToMany(TechnicianProfile::class, 'service_technician');
    }

    // Solicitudes de este servicio
    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class);
    }
}
