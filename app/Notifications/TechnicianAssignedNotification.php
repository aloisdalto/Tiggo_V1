<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TechnicianAssignedNotification extends Notification
{
    use Queueable;

    protected $serviceRequest;

    public function __construct($serviceRequest)
    {
        $this->serviceRequest = $serviceRequest;
    }

    public function via($notifiable)
    {
        return ['mail']; // o ['database', 'mail'] etc.
    }

    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
                    ->subject('Nueva solicitud asignada')
                    ->line('Se te ha asignado una nueva solicitud de servicio.')
                    ->action('Ver solicitud', url('/service-requests/'.$this->serviceRequest->id))
                    ->line('Gracias por usar nuestra plataforma.');
    }
}
