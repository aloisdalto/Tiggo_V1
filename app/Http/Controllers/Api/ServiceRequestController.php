<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Events\ServiceRequestCreated;
use App\Models\TechnicianProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceRequestController extends Controller
{
    // Crear solicitud y asignar técnico automáticamente
    public function store(Request $request)
    {
        // Validar datos básicos (puedes agregar más validaciones)
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'client_latitude' => 'required|numeric',
            'client_longitude' => 'required|numeric',
            'comments' => 'nullable|string',
        ]);
    
        // Crear la solicitud sin técnico asignado aún
        $serviceRequest = ServiceRequest::create([
            'cliente_id' => $request->user()->id,
            'service_id' => $request->service_id,
            'client_latitude' => $request->client_latitude,
            'client_longitude' => $request->client_longitude,
            'status' => 'pendiente',
            'comments' => $request->comments,
        ]);
    
        // Buscar técnico más cercano usando fórmula Haversine
        $lat = $request->client_latitude;
        $lng = $request->client_longitude;
    
        $technician = \DB::table('technician_profiles')
            ->select('user_id', 'latitude', 'longitude',
                \DB::raw("(6371 * acos(cos(radians($lat)) * cos(radians(latitude)) * cos(radians(longitude) - radians($lng)) + sin(radians($lat)) * sin(radians(latitude)))) AS distance")
            )
            ->orderBy('distance')
            ->first();
    
        if ($technician) {
            // Asignar técnico a la solicitud
            $serviceRequest->tecnico_id = $technician->user_id;
            $serviceRequest->status = 'asignado';
            $serviceRequest->save();
        
            // Opcional: disparar evento o notificación para técnico asignado
            event(new ServiceRequestCreated($serviceRequest));
        }
    
        return response()->json($serviceRequest, 201);
    }


    // Actualizar estado de solicitud (ejemplo: completar, cancelar)
    public function updateStatus(Request $request, ServiceRequest $serviceRequest)
    {
        $request->validate([
            'status' => 'required|in:pendiente,asignado,en_progreso,completado,cancelado',
        ]);

        $serviceRequest->status = $request->status;
        $serviceRequest->save();

        return response()->json(['message' => 'Estado actualizado', 'serviceRequest' => $serviceRequest]);
    }

    // Listar solicitudes del usuario autenticado
    public function index(Request $request)
    {
        $user = $request->user();

        $query = ServiceRequest::with('service', 'technician.user');

        if ($user->hasRole('tecnico')) {
            $query->where('tecnico_id', $user->id);
        } else {
            $query->where('cliente_id', $user->id);
        }

        $requests = $query->get();

        return response()->json($requests);
    }
}
