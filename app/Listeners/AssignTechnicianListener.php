<?php

namespace App\Listeners;

use App\Events\ServiceRequestCreated;
use App\Jobs\AssignTechnicianJob;

class AssignTechnicianListener
{
    public function handle(ServiceRequestCreated $event)
    {
        AssignTechnicianJob::dispatch($event->serviceRequest);
    }
}