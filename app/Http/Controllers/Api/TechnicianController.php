<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TechnicianProfile;
use Illuminate\Http\Request;

class TechnicianController extends Controller
{
    public function index(Request $request)
    {
        $query = TechnicianProfile::with('user', 'services')
            ->where('is_available', true);

        // Filtrar por servicio
        if ($request->has('service_id')) {
            $serviceId = $request->service_id;
            $query->whereHas('services', function($q) use ($serviceId) {
                $q->where('services.id', $serviceId);
            });
        }

        // Filtrar por ubicación (ejemplo: técnicos dentro de un radio)
        if ($request->has(['latitude', 'longitude', 'radius'])) {
            $lat = $request->latitude;
            $lng = $request->longitude;
            $radius = $request->radius; // en km

            // Aquí puedes usar fórmula Haversine o paquete espacial para filtrar
            // Por simplicidad, filtro básico (no exacto)
            $query->whereBetween('latitude', [$lat - 0.1, $lat + 0.1])
                  ->whereBetween('longitude', [$lng - 0.1, $lng + 0.1]);
        }

        // Ordenar por calificación promedio descendente
        $query->orderByDesc('average_rating');

        $technicians = $query->get();

        return response()->json($technicians);
    }
}
