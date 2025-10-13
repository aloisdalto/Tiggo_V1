<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    // Crear calificación
    public function store(Request $request)
    {
        $request->validate([
            'service_request_id' => 'required|exists:service_requests,id',
            'score' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $user = $request->user();

        $rating = Rating::create([
            'cliente_id' => $user->id,
            'service_request_id' => $request->service_request_id,
            'tecnico_id' => \App\Models\ServiceRequest::findOrFail($request->service_request_id)->tecnico_id,
            'score' => $request->score,
            'comment' => $request->comment,
        ]);

        return response()->json($rating, 201);
    }

    // Listar calificaciones de un técnico
    public function indexByTechnician($technicianId)
    {
        $ratings = Rating::where('tecnico_id', $technicianId)->get();

        return response()->json($ratings);
    }
}
