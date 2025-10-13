<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $fillable = [
        'cliente_id', 'tecnico_id', 'service_request_id', 'score', 'comment'
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'tecnico_id');
    }

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }
}
