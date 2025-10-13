<?php

namespace App\Jobs;

use App\Events\ServiceRequestCreated;
use App\Models\ServiceRequest;
use App\Models\TechnicianProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TechnicianAssignedNotification;
use App\Notifications\ClientNotification;

class AssignTechnicianJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $serviceRequest;

    public function __construct(ServiceRequest $serviceRequest)
    {
        $this->serviceRequest = $serviceRequest;
    }

    public function handle()
    {
        // Buscar técnico disponible con mejor rating que ofrezca el servicio
        $technician = TechnicianProfile::where('is_available', true)
            ->whereHas('services', function($q) {
                $q->where('id', $this->serviceRequest->service_id);
            })
            ->orderByDesc('average_rating')
            ->first();

        if ($technician) {
            // Asignar técnico a la solicitud
            $this->serviceRequest->tecnico_id = $technician->user_id;
            $this->serviceRequest->status = 'asignado';
            $this->serviceRequest->save();

            // Marcar técnico como no disponible
            $technician->is_available = false;
            $technician->save();

            // Enviar notificación al técnico
            Notification::send($technician->user, new TechnicianAssignedNotification($this->serviceRequest));

            // Enviar notificación al cliente
            Notification::send($this->serviceRequest->client, new ClientNotification($this->serviceRequest));
        }
    }
}
